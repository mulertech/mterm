<?php

namespace MulerTech\MTerm\Form\Validator;

class CallbackValidator extends AbstractValidator
{
    private $callback;

    public function __construct(
        callable $callback,
        string $errorMessage = "This value is not valid."
    ) {
        $this->callback = $callback;
        parent::__construct($errorMessage);
    }

    public function validate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $result = call_user_func($this->callback, $value);
        return $result === true ? null : ($result === false ? $this->errorMessage : $result);
    }
}