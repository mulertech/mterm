<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class RangeField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class RangeField extends NumberField
{
    private int $step = 1;

    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setMin(0);
        $this->setMax(100);
    }

    /**
     * @param int $step
     * @return $this
     */
    public function setStep(int $step): self
    {
        $this->step = max(1, $step);
        return $this;
    }

    /**
     * @return int
     */
    public function getStep(): int
    {
        return $this->step;
    }

    /**
     * @param string|int|float|array<int|string, string>|null $value
     * @return array<string>
     */
    public function validate(string|int|float|array|null $value): array
    {
        $errors = parent::validate($value);

        if ($value === '') {
            return $errors;
        }

        if (is_numeric($value)) {
            if ($this->step > 1 && ((int)$value % $this->step) !== 0) {
                $errors[] = "Value must be a multiple of $this->step.";
            }
        }

        return $errors;
    }
}
