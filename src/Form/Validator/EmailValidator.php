<?php

namespace MulerTech\MTerm\Form\Validator;

class EmailValidator extends AbstractValidator
{
    public function __construct(string $errorMessage = "Please enter a valid email address.")
    {
        parent::__construct($errorMessage);
    }

    public function validate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->errorMessage;
        }

        return null;
    }
}