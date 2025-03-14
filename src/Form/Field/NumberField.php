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
     * @return string|int|float|null
     */
    public function processInput(string $input): string|int|null|float
    {
        if ($input === '') {
            return $this->defaultValue;
        }

        return $this->allowFloat ? (float)$input : (int)$input;
    }

    /**
     * @param string|null $value
     * @return array
     */
    public function validate(?string $value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '') {
            if (!is_numeric($value)) {
                $errors[] = "Please enter a valid number.";
                return $errors;
            }

            if (!$this->allowFloat && floor($value) != $value) {
                $errors[] = "Please enter an integer value.";
            }

            if ($this->min !== null && $value < $this->min) {
                $errors[] = "Value must be at least {$this->min}.";
            }

            if ($this->max !== null && $value > $this->max) {
                $errors[] = "Value cannot exceed {$this->max}.";
            }
        }

        return $errors;
    }
}