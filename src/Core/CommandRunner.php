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
     * Exécute une commande système et retourne le résultat
     *
     * @param string $command La commande à exécuter
     * @return array Tableau contenant [output, returnCode]
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
     * Exécute une commande et capture les flux stdout et stderr séparément
     *
     * @param string $command La commande à exécuter
     * @return array Tableau contenant [stdout, stderr, returnCode]
     */
    public function runWithStderr(string $command): array
    {
        $descriptorspec = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w']   // stderr
        ];

        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            $returnCode = proc_close($process);

            return [
                'stdout' => $stdout,
                'stderr' => $stderr,
                'returnCode' => $returnCode
            ];
        }

        throw new \RuntimeException('Failed to execute command: ' . $command);
    }
}