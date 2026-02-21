<?php

namespace App\Http\Controllers;

use App\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LLMService;

class AIQueryController extends Controller
{
    public function query(Request $request, LLMService $llm)
    {
        if (config('ihandcashier.ai_enable') == false) {
            return Response::badRequest('AI is disabled');
        }
        $question = $request->input('question');
        // session(['chat_history' => []]);
        $response = $llm->ask($question);

        $msg = $response->choices[0]->message;

        $messages = session('chat_history');
        /*
        =====================================================
        =============== 1. OPENAI-STYLE function_call =======
        =====================================================
        */
        if (!empty($msg->toolCalls)) {

            $fn = $msg->toolCalls;
            $args = json_decode($fn[0]->function->arguments, true);

            if (!isset($args['query']) || empty($args['query'])) {
                return Response::badRequest('Tool call tanpa query');
            }
            $query = $args['query'];
            if (!$this->isSafeQuery($query)) {
                return Response::badRequest('Query tidak aman: ' . $query);
            }

            $data = DB::select($query);
            session(['chat_history' => array_slice($messages, -20)]);
            if (isset($request->summary) && $request->summary) {
                $final = $llm->formatAnswer($data, $messages);
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $final->choices[0]->message->content,
                ];

                return Response::ok('Loaded', [
                    'query' => $query,
                    'data' => $final->choices[0]->message->content
                ]);
            } else {
                return Response::ok('Loaded', [
                    'query' => $query,
                    'data' => $data
                ]);
            }
        } else {
            $messages[] = [
                'role' => 'assistant',
                'content' => $msg->content,
            ];
            session(['chat_history' => array_slice($messages, -20)]);
            return Response::ok('Loaded', [
                'query' => null,
                'data' => $msg->content
            ]);
        }
    }

    public function isSafeQuery($query)
    {
        $blacklist = [
            '/\bDROP\b/i',
            '/\bDELETE\b/i',
            '/\bUPDATE\b/i',
            '/\bINSERT\b/i',
            '/\bALTER\b/i',
            '/\bTRUNCATE\b/i',
        ];

        foreach ($blacklist as $rule) {
            if (preg_match($rule, $query)) {
                return false;
            }
        }

        return true;
    }
}
