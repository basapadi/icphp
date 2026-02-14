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

            if (!isset($args['query'])) {
                return Response::badRequest('Function call tanpa query');
            }

            $query = $args['query'];
            if (!$this->isSafeQuery($query)) {
                return Response::badRequest('Query tidak aman: ' . $query);
            }

            $data = DB::select($query);
            if (isset($request->summary) && $request->summary) {
                session()->put('chat_history', []);
                $final = $llm->formatAnswer($data, $messages);
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
        }

        /*
        =====================================================
        =============== 2. GROK-STYLE tool_calls ============
        =====================================================
        */
        if (!empty($msg->toolCalls) && count($msg->toolCalls) > 0) {

            $tool = $msg->toolCalls[0];  // Grok selalu array

            // Grok struktur:
            // tool_calls -> [{ function: { name, arguments } }]
            if (empty($tool->function)) {
                return Response::badRequest('Tool call tidak memiliki fungsi');
            }

            $fn = $tool->function;

            $args = json_decode($fn->arguments, true);

            if (!isset($args['query'])) {
                return Response::badRequest('Tool call tanpa query');
            }

            $query = $args['query'];

            if (!$this->isSafeQuery($query)) {
                return Response::badRequest('Query tidak aman: ' . $query);
            }


            $data = DB::select($query);

            if (isset($request->summary) && $request->summary) {

                $final = $llm->formatAnswer($data, $messages);
                return Response::ok('Loaded', $final->choices[0]->message->content);
            }

            return Response::ok('Loaded', $msg->content);
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
