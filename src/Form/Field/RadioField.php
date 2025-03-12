<?php

namespace MulerTech\MTerm\Form\Field;

class RadioField extends SelectField
{
    protected bool $multiple = false;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->multiple = false; // Radio buttons always have single selection
    }

    public function setMultiple(bool $multiple): self
    {
        // Override to prevent enabling multiple selection
        return $this;
    }
}