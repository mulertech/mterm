<?php

namespace MulerTech\MTerm\Form\Field;

use DateTime;

/**
 * Class DateField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class DateField extends TextField
{
    private string $format = 'Y-m-d';

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $input
     * @return string|int|float|array<int|string, string>
     */
    public function processInput(string $input): string|int|float|array
    {
        if ($input === '' && !is_null($this->defaultValue) && !is_array($this->defaultValue)) {
            return $this->defaultValue;
        }

        $date = DateTime::createFromFormat($this->format, $input);
        if ($date === false) {
            return $input; // Return raw input for validation to catch error
        }

        return $date->format($this->format);
    }

    /**
     * @param string|int|float|array<int|string, string>|null $value
     * @return array<string>
     */
    public function validate(string|int|float|array|null $value): array
    {
        $errors = parent::validate($value);

        if ($value === '') {
            return $errors;
        }

        if (is_string($value)) {
            $date = DateTime::createFromFormat($this->format, $value);

            $dateErrors = DateTime::getLastErrors();

            if ($date === false || ($dateErrors !== false && $dateErrors['warning_count'] > 0)) {
                $errors[] = "Please enter a valid date in $this->format format.";
            }
        }

        return $errors;
    }
}
