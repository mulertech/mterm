<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class CheckboxField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class CheckboxField extends AbstractField
{
    private string $checkedValue = '1';
    private string $uncheckedValue = '0';

    /**
     * @param string $value
     * @return $this
     */
    public function setCheckedValue(string $value): self
    {
        $this->checkedValue = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUncheckedValue(string $value): self
    {
        $this->uncheckedValue = $value;
        return $this;
    }

    /**
     * @param string $input
     * @return string|int|null|float
     */
    public function processInput(string $input): string|int|null|float
    {
        if ($input === '') {
            return $this->defaultValue;
        }

        $input = strtolower(trim($input));
        return in_array($input, ['yes', 'y', 'true', 't', '1', $this->checkedValue], true)
            ? $this->checkedValue
            : $this->uncheckedValue;
    }
}