<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class NotEmptyValidator.
 *
 * @author Sébastien Muler
 */
class NotEmptyValidator extends AbstractValidator
{
    public function __construct(string $errorMessage = 'This value cannot be empty.')
    {
        parent::__construct($errorMessage);
    }

    public function validate(mixed $value): ?string
    {
        if (null === $value || '' === $value || (is_array($value) && empty($value))) {
            return $this->errorMessage;
        }

        return null;
    }
}
