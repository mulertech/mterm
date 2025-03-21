<?php

namespace MulerTech\MTerm\Form\Field;

use MulerTech\MTerm\Core\Terminal;

/**
 * Class PasswordField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class PasswordField extends TextField
{
    private bool $maskInput = true;
    private string $maskChar = '*';

    /**
     * @return bool
     */
    public function isMaskInput(): bool
    {
        return $this->maskInput;
    }

    /**
     * @param bool $maskInput
     * @return $this
     */
    public function setMaskInput(bool $maskInput = true): self
    {
        $this->maskInput = $maskInput;
        return $this;
    }

    /**
     * @return string
     */
    public function getMaskChar(): string
    {
        return $this->maskChar;
    }

    /**
     * @param string $maskChar
     * @return $this
     */
    public function setMaskChar(string $maskChar): self
    {
        $this->maskChar = $maskChar;
        return $this;
    }

    /**
     * @param string $input
     * @return string|int|float|array<int|string, string>
     */
    public function parseInput(string $input): string|int|float|array
    {
        if ($input === '' && !is_null($this->defaultValue)) {
            return $this->defaultValue;
        }

        return $input;
    }

    /**
     * Process the password input with masking
     *
     * @return string|int|float|array<int|string, string>
     */
    public function processInput(string $input = ''): string|int|float|array
    {
        if (!$this->maskInput) {
            return parent::processInput($input);
        }

        if ($this->terminal === null) {
            throw new \RuntimeException('Terminal must be set before calling processInput');
        }

        $this->clearErrors();

        $prompt = $this->buildPrompt();

        $this->terminal->write($prompt);
        $this->terminal->specialMode();

        $password = '';

        while (true) {
            $char = $this->terminal->readChar();

            // Enter key pressed
            if ($char === PHP_EOL) {
                $this->terminal->writeLine('');
                break;
            }

            // Backspace handling
            if ($char === "\x7F" || $char === "\x08") {
                if (strlen($password) > 0) {
                    $password = substr($password, 0, -1);
                    $this->terminal->write("\x08 \x08");
                }
            } // Regular character
            elseif (ord($char) >= 32) {
                $password .= $char;
                $this->terminal->write($this->getMaskChar());
            }
        }

        $this->terminal->normalMode();
        return $this->parseInput($password);
    }

    /**
     * @return string
     */
    private function buildPrompt(): string
    {
        $label = $this->getLabel();
        $required = $this->isRequired() ? ' (required)' : '';
        return "$label$required: ";
    }
}
