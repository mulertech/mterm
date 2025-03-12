<?php

namespace MulerTech\MTerm\Form\Validator;

use DateTime;
use DateTimeInterface;

class DateValidator extends AbstractValidator
{
    private string $format;
    private ?DateTimeInterface $minDate;
    private ?DateTimeInterface $maxDate;

    public function __construct(
        string             $format = 'Y-m-d',
        ?DateTimeInterface $minDate = null,
        ?DateTimeInterface $maxDate = null,
        ?string            $errorMessage = null
    ) {
        $this->format = $format;
        $this->minDate = $minDate;
        $this->maxDate = $maxDate;

        if ($errorMessage === null) {
            $errorMessage = "Please enter a valid date in {$format} format.";
        }

        parent::__construct($errorMessage);
    }

    public function validate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $date = DateTime::createFromFormat($this->format, $value);
        if ($date === false) {
            return $this->errorMessage;
        }

        // Check if the format is exactly as expected (no additional characters)
        $errors = DateTime::getLastErrors();
        if ($errors['warning_count'] > 0 || $errors['error_count'] > 0) {
            return $this->errorMessage;
        }

        if ($this->minDate !== null && $date < $this->minDate->setTime(0, 0)) {
            return "Date must be on or after " . $this->minDate->format($this->format);
        }

        if ($this->maxDate !== null && $date > $this->maxDate->setTime(23, 59, 59)) {
            return "Date must be on or before " . $this->maxDate->format($this->format);
        }

        return null;
    }
}