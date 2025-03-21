<?php

namespace MulerTech\MTerm\Core;

/**
 * Class CommandRunner
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class CommandRunner
{
    /**
     * Execute a command and capture the output and return code
     *
     * @param string $command
     * @return array{output: array<int, string>, returnCode: int}
     */
    public function run(string $command): array
    {
        $output = [];
        $returnCode = 0;

        exec($command . ' 2>&1', $output, $returnCode);

        return [
            'output' => $output,
            'returnCode' => $returnCode
        ];
    }

    /**
     * @param string $command
     * @return array{stdout: false|string, stderr: false|string, returnCode: int}
     */
    public function runWithStderr(string $command): array
    {
        $descriptorSpec = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w']   // stderr
        ];

        $pipes = [];
        $process = proc_open($command, $descriptorSpec, $pipes);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = $process ? proc_close($process) : -1;

        return [
            'stdout' => $stdout,
            'stderr' => $stderr,
            'returnCode' => $returnCode
        ];
    }
}
