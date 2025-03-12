<?php

namespace MulerTech\MTerm\Form\Field;

class SelectField extends AbstractField
{
    protected array $options = [];
    protected bool $multiple = false;

    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;
        return $this;
    }

    public function processInput($input)
    {
        if ($input === '') {
            return $this->defaultValue;
        }

        if ($this->multiple) {
            $values = array_map('trim', explode(',', $input));
            return array_intersect($values, array_keys($this->options));
        }

        return $input;
    }

    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '') {
            if ($this->multiple) {
                foreach ($value as $val) {
                    if (!array_key_exists($val, $this->options)) {
                        $errors[] = "Invalid option: '{$val}'.";
                    }
                }
            } else if (!array_key_exists($value, $this->options)) {
                $errors[] = "Invalid option: '{$value}'.";
            }
        }

        return $errors;
    }
}