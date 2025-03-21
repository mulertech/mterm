<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class ChoiceValidator
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class ChoiceValidator extends AbstractValidator
{
    /**
     * @var array<int|string, mixed>
     */
    private array $choices;
    private bool $strict;

    /**
     * @param array<int|string, mixed> $choices
     * @param bool $strict
     * @param string $errorMessage
     */
    public function __construct(
        array $choices,
        bool $strict = true,
        string $errorMessage = "Selected value is not a valid choice."
    ) {
        $this->choices = $choices;
        $this->strict = $strict;
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

        if (!in_array($value, $this->choices, $this->strict)) {
            return $this->errorMessage;
        }

        return null;
    }
}
