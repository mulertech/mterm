<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class RegexValidator.
 *
 * @author Sébastien Muler
 */
class RegexValidator extends AbstractValidator
{
    private string $pattern;

    public function __construct(
        string $pattern,
        string $errorMessage = 'This value is not valid.',
    ) {
        $this->pattern = $pattern;
        parent::__construct($errorMessage);
    }

    public function validate(mixed $value): ?string
    {
        if (null === $value || '' === $value || !is_string($value)) {
            return null;
        }

        if (!preg_match($this->pattern, $value)) {
            return $this->errorMessage;
        }

        return null;
    }
}
