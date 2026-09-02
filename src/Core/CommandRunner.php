<?php

namespace MulerTech\MTerm\Core;

/**
 * Class CommandRunner.
 *
 * @author Sébastien Muler
 */
class CommandRunner
{
    /**
     * Execute a command and capture the output and return code.
     *
     * @return array{output: array<int, string>, returnCode: int}
     */
    public function run(string $command): array
    {
        $output = [];
        $returnCode = 0;

        exec($command.' 2>&1', $output, $returnCode);

        return [
            'output' => $output,
            'returnCode' => $returnCode,
        ];
    }

    /**
     * Execute a command and capture its two streams separately.
     *
     * @return array{stdout: false|string, stderr: false|string, returnCode: int}
     */
    public function runWithStderr(string $command): array
    {
        $descriptorSpec = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],   // stderr
        ];

        $pipes = [];
        $process = proc_open($command, $descriptorSpec, $pipes);

        if (false === $process) {
            throw new \RuntimeException(sprintf('Unable to start the command: %s', $command));
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        return [
            'stdout' => $stdout,
            'stderr' => $stderr,
            'returnCode' => $returnCode,
        ];
    }

    /**
     * Execute a command that writes to the terminal itself.
     *
     * Deployment logs and test suites are watched as they unfold; capturing
     * their output to display it afterwards would trade that for a tidier
     * summary. Nothing here goes through the display path.
     *
     * @return int Exit code of the command
     */
    public function runDirect(string $command): int
    {
        $returnCode = 0;

        system($command, $returnCode);

        return $returnCode;
    }
}
