<?php

namespace MulerTech\MTerm\Core\Output;

/**
 * Output writing to a stream, the standard output unless another one is given.
 *
 * @author Sébastien Muler
 */
class StreamOutput implements OutputInterface
{
    /**
     * @var resource
     */
    private $stream;
    private ?bool $decorated;

    /**
     * @param resource|null $stream    Defaults to the standard output
     * @param bool|null     $decorated Forces decoration instead of detecting it
     */
    public function __construct($stream = null, ?bool $decorated = null)
    {
        $stream ??= defined('STDOUT') ? STDOUT : fopen('php://stdout', 'wb');

        if (!is_resource($stream)) {
            throw new \RuntimeException('Unable to open the standard output stream.');
        }

        $this->stream = $stream;
        $this->decorated = $decorated;
    }

    public function write(string $text): void
    {
        if (false === fwrite($this->stream, $text)) {
            throw new \RuntimeException('Unable to write to the output stream.');
        }
    }

    public function isDecorated(): bool
    {
        return $this->decorated ??= $this->detectDecoration();
    }

    private function detectDecoration(): bool
    {
        $noColor = getenv('NO_COLOR');

        if (is_string($noColor) && '' !== $noColor) {
            return false;
        }

        if (!stream_isatty($this->stream)) {
            return false;
        }

        if (DIRECTORY_SEPARATOR === '/') {
            return true;
        }

        return function_exists('sapi_windows_vt100_support')
            && sapi_windows_vt100_support($this->stream);
    }
}
