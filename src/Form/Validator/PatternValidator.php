<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class PatternValidator
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class PatternValidator extends AbstractValidator
{
    private string $pattern;

    /**
     * @param string $pattern
     * @param string $errorMessage
     */
    public function __construct(string $pattern, string $errorMessage = "Value does not match required pattern.")
    {
        $this->pattern = $pattern;
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

        if (!preg_match($this->pattern, $value)) {
            return $this->errorMessage;
        }

        return null;
    }
}
