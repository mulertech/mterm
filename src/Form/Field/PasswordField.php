<?php

namespace MulerTech\MTerm\Form\Field;

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
     * @return string|int|float|null
     */
    public function processInput(string $input): string|int|null|float
    {
        if ($input === '') {
            return $this->defaultValue;
        }
        return $input;
    }
}