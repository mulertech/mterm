<?php

namespace MulerTech\MTerm\Form\Validator;

class NotEmptyValidator extends AbstractValidator
{
    public function __construct(string $errorMessage = "This value cannot be empty.")
    {
        parent::__construct($errorMessage);
    }

    public function validate($value): ?string
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            return $this->errorMessage;
        }

        return null;
    }
}