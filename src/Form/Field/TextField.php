<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class TextField.
 *
 * @author Sébastien Muler
 */
class TextField extends AbstractField
{
    protected int $minLength = 0;
    protected ?int $maxLength = null;

    /**
     * @return $this
     */
    public function setMinLength(int $minLength): self
    {
        $this->minLength = $minLength;

        return $this;
    }

    /**
     * @return $this
     */
    public function setMaxLength(?int $maxLength): self
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * @param string|int|float|array<int|string, string>|null $value
     *
     * @return array<string>
     */
    public function validate(string|int|float|array|null $value): array
    {
        $errors = parent::validate($value);

        if ('' === $value) {
            return $errors;
        }

        if (is_string($value)) {
            $length = mb_strlen($value);

            if ($length < $this->minLength) {
                $errors[] = "This field must be at least $this->minLength characters long.";
            }

            if (null !== $this->maxLength && $length > $this->maxLength) {
                $errors[] = "This field cannot exceed $this->maxLength characters.";
            }
        }

        return $errors;
    }
}
