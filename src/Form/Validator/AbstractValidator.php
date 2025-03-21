<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class AbstractValidator
 * @package MulerTech\MTerm\Form\Validator
 * @author Sébastien Muler
 */
abstract class AbstractValidator implements ValidatorInterface
{
    protected string $errorMessage;

    /**
     * @param string $errorMessage
     */
    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
