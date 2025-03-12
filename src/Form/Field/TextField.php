<?php

namespace MulerTech\MTerm\Form\Field;

class TextField extends AbstractField
{
    protected int $minLength = 0;
    protected ?int $maxLength = null;

    public function setMinLength(int $minLength): self
    {
        $this->minLength = $minLength;
        return $this;
    }

    public function setMaxLength(?int $maxLength): self
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '') {
            $length = mb_strlen($value);

            if ($length < $this->minLength) {
                $errors[] = "This field must be at least {$this->minLength} characters long.";
            }

            if ($this->maxLength !== null && $length > $this->maxLength) {
                $errors[] = "This field cannot exceed {$this->maxLength} characters.";
            }
        }

        return $errors;
    }
}