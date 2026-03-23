<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class IpAddressValidator.
 *
 * @author Sébastien Muler
 */
class IpAddressValidator extends AbstractValidator
{
    private int $flags;

    public function __construct(
        bool $allowIPv4 = true,
        bool $allowIPv6 = true,
        bool $allowPrivate = true,
        bool $allowReserved = true,
        string $errorMessage = 'Please enter a valid IP address.',
    ) {
        $this->flags = 0;
        if (!$allowIPv4) {
            $this->flags |= FILTER_FLAG_IPV6;
        }
        if (!$allowIPv6) {
            $this->flags |= FILTER_FLAG_IPV4;
        }
        if (!$allowPrivate) {
            $this->flags |= FILTER_FLAG_NO_PRIV_RANGE;
        }
        if (!$allowReserved) {
            $this->flags |= FILTER_FLAG_NO_RES_RANGE;
        }

        parent::__construct($errorMessage);
    }

    public function validate(mixed $value): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!filter_var($value, FILTER_VALIDATE_IP, $this->flags)) {
            return $this->errorMessage;
        }

        return null;
    }
}
