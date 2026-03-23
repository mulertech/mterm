<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class LengthValidator.
 *
 * @author Sébastien Muler
 */
class LengthValidator extends AbstractValidator
{
    private ?int $min;
    private ?int $max;

    public function __construct(
        ?int $min = null,
        ?int $max = null,
        ?string $errorMessage = null,
    ) {
        $this->min = $min;
        $this->max = $max;

        if (null === $errorMessage) {
            if (null !== $min && null !== $max) {
                $errorMessage = "Value must be between $min and $max characters.";
            } elseif (null !== $min) {
                $errorMessage = "Value must be at least $min characters.";
            } elseif (null !== $max) {
                $errorMessage = "Value cannot exceed $max characters.";
            } else {
                $errorMessage = 'Invalid length.';
            }
        }

        parent::__construct($errorMessage);
    }

    public function validate(mixed $value): ?string
    {
        if (null === $value || '' === $value || !is_scalar($value)) {
            return null;
        }

        $length = mb_strlen((string) $value);

        if (null !== $this->min && $length < $this->min) {
            return $this->errorMessage;
        }

        if (null !== $this->max && $length > $this->max) {
            return $this->errorMessage;
        }

        return null;
    }
}
