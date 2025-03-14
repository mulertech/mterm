<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class TextField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class TextField extends AbstractField
{
    protected int $minLength = 0;
    protected ?int $maxLength = null;

    /**
     * @param int $minLength
     * @return $this
     */
    public function setMinLength(int $minLength): self
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * @param int|null $maxLength
     * @return $this
     */
    public function setMaxLength(?int $maxLength): self
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * @param string|null $value
     * @return array
     */
    public function validate(?string $value): array
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