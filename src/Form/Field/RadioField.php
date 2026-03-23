<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class RadioField.
 *
 * @author Sébastien Muler
 */
class RadioField extends SelectField
{
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->cursorPresent = 'o';
    }

    /**
     * @return $this
     */
    public function setMultipleSelection(bool $multipleSelection = true): self
    {
        // Radio fields can't have multiple selection
        return $this;
    }
}
