<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class ChoiceValidator.
 *
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
     */
    public function __construct(
        array $choices,
        bool $strict = true,
        string $errorMessage = 'Selected value is not a valid choice.',
    ) {
        $this->choices = $choices;
        $this->strict = $strict;
        parent::__construct($errorMessage);
    }

    public function validate(mixed $value): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!in_array($value, $this->choices, $this->strict)) {
            return $this->errorMessage;
        }

        return null;
    }
}
