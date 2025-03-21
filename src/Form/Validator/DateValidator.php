<?php

namespace MulerTech\MTerm\Form\Validator;

use DateTime;
use DateTimeInterface;

/**
 * Class DateValidator
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class DateValidator extends AbstractValidator
{
    private string $format;
    private ?DateTimeInterface $minDate;
    private ?DateTimeInterface $maxDate;

    /**
     * @param string $format
     * @param DateTimeInterface|null $minDate
     * @param DateTimeInterface|null $maxDate
     * @param string|null $errorMessage
     */
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
            $errorMessage = "Please enter a valid date in $format format.";
        }

        parent::__construct($errorMessage);
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function validate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $date = DateTime::createFromFormat($this->format, (string)$value);
        if ($date === false) {
            return $this->errorMessage;
        }

        // Check if the format is exactly as expected (no additional characters)
        $errors = DateTime::getLastErrors();
        if ($errors !== false && $errors['warning_count'] > 0) {
            return $this->errorMessage;
        }

        if ($this->minDate !== null) {
            $minClone = clone $this->minDate;
            $minTime = (new DateTime())->setTimestamp($minClone->getTimestamp())->setTime(0, 0);
            if ($date < $minTime) {
                return "Date must be on or after " . $this->minDate->format($this->format);
            }
        }

        if ($this->maxDate !== null) {
            $maxClone = clone $this->maxDate;
            $maxTime = (new DateTime())->setTimestamp($maxClone->getTimestamp())->setTime(23, 59, 59);
            if ($date > $maxTime) {
                return "Date must be on or before " . $this->maxDate->format($this->format);
            }
        }

        return null;
    }
}
