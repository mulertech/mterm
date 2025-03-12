<?php

namespace MulerTech\MTerm\Form\Field;

class PasswordField extends TextField
{
    private bool $maskInput = true;
    private string $maskChar = '*';

    public function setMaskInput(bool $maskInput): self
    {
        $this->maskInput = $maskInput;
        return $this;
    }

    public function setMaskChar(string $maskChar): self
    {
        $this->maskChar = $maskChar;
        return $this;
    }

    public function processInput($input)
    {
        if ($input === '') {
            return $this->defaultValue;
        }
        return $input;
    }
}