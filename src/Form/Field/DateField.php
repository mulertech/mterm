<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class DateField.
 *
 * @author Sébastien Muler
 */
class DateField extends TextField
{
    private string $format = 'Y-m-d';

    /**
     * @return $this
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string|int|float|array<int|string, string>
     */
    public function processInput(string $input): string|int|float|array
    {
        if ('' === $input && !is_null($this->defaultValue) && !is_array($this->defaultValue)) {
            return $this->defaultValue;
        }

        $date = \DateTime::createFromFormat($this->format, $input);
        if (false === $date) {
            return $input; // Return raw input for validation to catch error
        }

        return $date->format($this->format);
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
            $date = \DateTime::createFromFormat($this->format, $value);

            $dateErrors = \DateTime::getLastErrors();

            if (false === $date || (false !== $dateErrors && $dateErrors['warning_count'] > 0)) {
                $errors[] = "Please enter a valid date in $this->format format.";
            }
        }

        return $errors;
    }
}
