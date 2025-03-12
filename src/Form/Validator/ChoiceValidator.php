<?php

namespace MulerTech\MTerm\Form\Validator;

class ChoiceValidator extends AbstractValidator
{
    private array $choices;
    private bool $strict;

    public function __construct(
        array $choices,
        bool $strict = true,
        string $errorMessage = "Selected value is not a valid choice."
    ) {
        $this->choices = $choices;
        $this->strict = $strict;
        parent::__construct($errorMessage);
    }

    public function validate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!in_array($value, $this->choices, $this->strict)) {
            return $this->errorMessage;
        }

        return null;
    }
}