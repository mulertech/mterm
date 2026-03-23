<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class PasswordField.
 *
 * @author Sébastien Muler
 */
class PasswordField extends TextField
{
    private bool $maskInput = true;
    private string $maskChar = '*';

    public function isMaskInput(): bool
    {
        return $this->maskInput;
    }

    /**
     * @return $this
     */
    public function setMaskInput(bool $maskInput = true): self
    {
        $this->maskInput = $maskInput;

        return $this;
    }

    public function getMaskChar(): string
    {
        return $this->maskChar;
    }

    /**
     * @return $this
     */
    public function setMaskChar(string $maskChar): self
    {
        $this->maskChar = $maskChar;

        return $this;
    }

    public function parseInput(string $input): string
    {
        if ('' === $input && is_string($this->defaultValue)) {
            return $this->defaultValue;
        }

        return $input;
    }

    /**
     * Process the password input with masking.
     */
    public function processInput(string $input = ''): string
    {
        if (!$this->maskInput) {
            $result = parent::processInput($input);

            return is_string($result) ? $result : $input;
        }

        $terminal = $this->terminal;

        if (null === $terminal) {
            throw new \RuntimeException('Terminal must be set before calling processInput');
        }

        $this->clearErrors();

        $prompt = $this->buildPrompt();

        $terminal->write($prompt);
        $terminal->specialMode();

        $password = '';

        while (true) {
            $char = $terminal->readChar();

            // Enter key pressed
            if (PHP_EOL === $char) {
                $terminal->writeLine('');
                break;
            }

            // Backspace handling
            if ("\x7F" === $char || "\x08" === $char) {
                if ('' !== $password) {
                    $password = substr($password, 0, -1);
                    $terminal->write("\x08 \x08");
                }
            } // Regular character
            elseif (ord($char) >= 32) {
                $password .= $char;
                $terminal->write($this->getMaskChar());
            }
        }

        $terminal->normalMode();

        return $this->parseInput($password);
    }

    /**
     * Build the prompt string.
     */
    private function buildPrompt(): string
    {
        $label = $this->getLabel();
        $required = $this->isRequired() ? ' (required)' : '';

        return "$label$required: ";
    }
}
