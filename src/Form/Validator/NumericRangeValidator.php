<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class NumericRangeValidator.
 *
 * @author Sébastien Muler
 */
class NumericRangeValidator extends AbstractValidator
{
    private ?float $min;
    private ?float $max;

    public function __construct(
        ?float $min = null,
        ?float $max = null,
        ?string $errorMessage = null,
    ) {
        $this->min = $min;
        $this->max = $max;

        if (null === $errorMessage) {
            if (null !== $min && null !== $max) {
                $errorMessage = "Value must be between $min and $max.";
            } elseif (null !== $min) {
                $errorMessage = "Value must be at least $min.";
            } elseif (null !== $max) {
                $errorMessage = "Value cannot exceed $max.";
            } else {
                $errorMessage = 'Invalid number.';
            }
        }

        parent::__construct($errorMessage);
    }

    public function validate(mixed $value): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_numeric($value)) {
            return 'Value must be a number.';
        }

        $numericValue = (float) $value;

        if (null !== $this->min && $numericValue < $this->min) {
            return $this->errorMessage;
        }

        if (null !== $this->max && $numericValue > $this->max) {
            return $this->errorMessage;
        }

        return null;
    }
}
