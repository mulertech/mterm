<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class NotEmptyValidator
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class NotEmptyValidator extends AbstractValidator
{
    /**
     * @param string $errorMessage
     */
    public function __construct(string $errorMessage = "This value cannot be empty.")
    {
        parent::__construct($errorMessage);
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function validate(mixed $value): ?string
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            return $this->errorMessage;
        }

        return null;
    }
}
