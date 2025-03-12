<?php

namespace MulerTech\MTerm\Form\Field;

class RangeField extends NumberField
{
    private int $step = 1;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setMin(0);
        $this->setMax(100);
    }

    public function setStep(int $step): self
    {
        $this->step = max(1, $step);
        return $this;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '') {
            if ($this->step > 1 && ($value % $this->step) !== 0) {
                $errors[] = "Value must be a multiple of {$this->step}.";
            }
        }

        return $errors;
    }
}