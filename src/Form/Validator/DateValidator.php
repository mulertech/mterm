<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class DateValidator.
 *
 * @author Sébastien Muler
 */
class DateValidator extends AbstractValidator
{
    private string $format;
    private ?\DateTimeInterface $minDate;
    private ?\DateTimeInterface $maxDate;

    public function __construct(
        string $format = 'Y-m-d',
        ?\DateTimeInterface $minDate = null,
        ?\DateTimeInterface $maxDate = null,
        ?string $errorMessage = null,
    ) {
        $this->format = $format;
        $this->minDate = $minDate;
        $this->maxDate = $maxDate;

        if (null === $errorMessage) {
            $errorMessage = "Please enter a valid date in $format format.";
        }

        parent::__construct($errorMessage);
    }

    public function validate(mixed $value): ?string
    {
        if (null === $value || '' === $value || !is_scalar($value)) {
            return null;
        }

        $date = \DateTime::createFromFormat($this->format, (string) $value);
        if (false === $date) {
            return $this->errorMessage;
        }

        // Check if the format is exactly as expected (no additional characters)
        $errors = \DateTime::getLastErrors();
        if (false !== $errors && $errors['warning_count'] > 0) {
            return $this->errorMessage;
        }

        if (null !== $this->minDate) {
            $minClone = clone $this->minDate;
            $minTime = (new \DateTime())->setTimestamp($minClone->getTimestamp())->setTime(0, 0);
            if ($date < $minTime) {
                return 'Date must be on or after '.$this->minDate->format($this->format);
            }
        }

        if (null !== $this->maxDate) {
            $maxClone = clone $this->maxDate;
            $maxTime = (new \DateTime())->setTimestamp($maxClone->getTimestamp())->setTime(23, 59, 59);
            if ($date > $maxTime) {
                return 'Date must be on or before '.$this->maxDate->format($this->format);
            }
        }

        return null;
    }
}
