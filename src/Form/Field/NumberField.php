<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class NumberField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class NumberField extends AbstractField
{
    protected ?float $min = null;
    protected ?float $max = null;
    protected bool $allowFloat = true;

    /**
     * @param float|null $min
     * @return $this
     */
    public function setMin(?float $min): self
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param float|null $max
     * @return $this
     */
    public function setMax(?float $max): self
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param bool $allowFloat
     * @return $this
     */
    public function setAllowFloat(bool $allowFloat): self
    {
        $this->allowFloat = $allowFloat;
        return $this;
    }

    /**
     * @param string $input
     * @return string|int|float|array<int|string, string>
     */
    public function processInput(string $input): string|int|float|array
    {
        if ($input === '' && !is_null($this->defaultValue)) {
            return $this->defaultValue;
        }

        return $this->allowFloat ? (float)$input : (int)$input;
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

        if (is_numeric($value) || is_string($value)) {
            if (!is_numeric($value)) {
                $errors[] = "Please enter a valid number.";
                return $errors;
            }

            if (!$this->allowFloat && floor((int)$value) != $value) {
                $errors[] = "Please enter an integer value.";
            }

            if ($this->min !== null && $value < $this->min) {
                $errors[] = "Value must be at least $this->min.";
            }

            if ($this->max !== null && $value > $this->max) {
                $errors[] = "Value cannot exceed $this->max.";
            }
        }

        return $errors;
    }
}
