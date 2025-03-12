<?php

namespace MulerTech\MTerm\Form\Validator;

class NumericRangeValidator extends AbstractValidator
{
    private ?float $min;
    private ?float $max;

    public function __construct(
        ?float $min = null,
        ?float $max = null,
        ?string $errorMessage = null
    ) {
        $this->min = $min;
        $this->max = $max;

        if ($errorMessage === null) {
            if ($min !== null && $max !== null) {
                $errorMessage = "Value must be between {$min} and {$max}.";
            } elseif ($min !== null) {
                $errorMessage = "Value must be at least {$min}.";
            } elseif ($max !== null) {
                $errorMessage = "Value cannot exceed {$max}.";
            } else {
                $errorMessage = "Invalid number.";
            }
        }

        parent::__construct($errorMessage);
    }

    public function validate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return "Value must be a number.";
        }

        $numericValue = (float) $value;

        if ($this->min !== null && $numericValue < $this->min) {
            return $this->errorMessage;
        }

        if ($this->max !== null && $numericValue > $this->max) {
            return $this->errorMessage;
        }

        return null;
    }
}