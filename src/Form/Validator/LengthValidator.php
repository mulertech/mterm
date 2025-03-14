<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class LengthValidator
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class LengthValidator extends AbstractValidator
{
    private ?int $min;
    private ?int $max;

    /**
     * @param int|null $min
     * @param int|null $max
     * @param string|null $errorMessage
     */
    public function __construct(
        ?int $min = null,
        ?int $max = null,
        ?string $errorMessage = null
    ) {
        $this->min = $min;
        $this->max = $max;

        if ($errorMessage === null) {
            if ($min !== null && $max !== null) {
                $errorMessage = "Value must be between {$min} and {$max} characters.";
            } elseif ($min !== null) {
                $errorMessage = "Value must be at least {$min} characters.";
            } elseif ($max !== null) {
                $errorMessage = "Value cannot exceed {$max} characters.";
            } else {
                $errorMessage = "Invalid length.";
            }
        }

        parent::__construct($errorMessage);
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function validate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $length = mb_strlen($value);

        if ($this->min !== null && $length < $this->min) {
            return $this->errorMessage;
        }

        if ($this->max !== null && $length > $this->max) {
            return $this->errorMessage;
        }

        return null;
    }
}