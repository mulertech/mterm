<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class EmailValidator.
 *
 * @author Sébastien Muler
 */
class EmailValidator extends AbstractValidator
{
    public function __construct(string $errorMessage = 'Please enter a valid email address.')
    {
        parent::__construct($errorMessage);
    }

    public function validate(mixed $value): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->errorMessage;
        }

        return null;
    }
}
