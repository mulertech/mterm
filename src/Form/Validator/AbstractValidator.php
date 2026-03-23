<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class AbstractValidator.
 *
 * @author Sébastien Muler
 */
abstract class AbstractValidator implements ValidatorInterface
{
    protected string $errorMessage;

    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
