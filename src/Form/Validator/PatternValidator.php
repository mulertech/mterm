<?php

namespace MulerTech\MTerm\Form\Validator;

class PatternValidator extends AbstractValidator
{
    private string $pattern;

    public function __construct(string $pattern, string $errorMessage = "Value does not match required pattern.")
    {
        $this->pattern = $pattern;
        parent::__construct($errorMessage);
    }

    public function validate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!preg_match($this->pattern, $value)) {
            return $this->errorMessage;
        }

        return null;
    }
}