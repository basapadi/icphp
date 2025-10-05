<?php

namespace App\Services;

class PythonRunner
{
    protected string $pythonPath;

    public function __construct()
    {
        $os = PHP_OS_FAMILY;
        $this->pythonPath = match ($os) {
            'Windows' => base_path('resources/python/win/python.exe'),
            'Darwin'  => base_path('resources/python/mac/bin/python3'),
            'Linux'   => base_path('resources/python/linux/bin/python3'),
            default   => 'python3',
        };
    }

    /**
     * Jalankan script Python dan ambil hasil output JSON.
     */
    public function run(string $script, array $args = []): mixed
    {
        $scriptPath = base_path("resources/python/scripts/{$script}.py");
        if (!file_exists($scriptPath)) {
            throw new \Exception("Script not found: {$scriptPath}");
        }

        $command = escapeshellcmd("{$this->pythonPath} {$scriptPath}");
        if ($args) {
            $jsonArgs = escapeshellarg(json_encode($args));
            $command .= " {$jsonArgs}";
        }

        $output = shell_exec($command);
        return json_decode($output, true);
    }
}
