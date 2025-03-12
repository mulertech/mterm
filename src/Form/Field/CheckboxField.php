<?php

namespace MulerTech\MTerm\Form\Field;

class CheckboxField extends AbstractField
{
    private string $checkedValue = '1';
    private string $uncheckedValue = '0';

    public function setCheckedValue(string $value): self
    {
        $this->checkedValue = $value;
        return $this;
    }

    public function setUncheckedValue(string $value): self
    {
        $this->uncheckedValue = $value;
        return $this;
    }

    public function processInput($input)
    {
        if ($input === '') {
            return $this->defaultValue;
        }

        $input = strtolower(trim($input));
        return in_array($input, ['yes', 'y', 'true', 't', '1', $this->checkedValue], true)
            ? $this->checkedValue
            : $this->uncheckedValue;
    }
}