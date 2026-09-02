<?php

namespace MulerTech\MTerm\Form\Field;

use MulerTech\MTerm\Core\Input\Key;

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
        $terminal->enableRawMode();

        $password = '';

        while (true) {
            $press = $terminal->readKey();

            if ($press->isEndOfInput() || $press->is(Key::Enter)) {
                $terminal->writeLine();
                break;
            }

            if ($press->is(Key::Backspace)) {
                if ('' !== $password) {
                    $password = mb_substr($password, 0, -1);
                    $terminal->write("\x08 \x08");
                }

                continue;
            }

            if ($press->isCharacter() && !$this->isControlCharacter($press->character)) {
                $password .= $press->character;
                $terminal->write($this->getMaskChar());
            }
        }

        $terminal->disableRawMode();

        return $this->parseInput($password);
    }

    /**
     * A character the terminal would not display, such as a Ctrl combination.
     */
    private function isControlCharacter(string $character): bool
    {
        return 1 === strlen($character) && ord($character) < 32;
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
