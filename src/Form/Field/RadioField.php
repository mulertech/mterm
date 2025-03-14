<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class RadioField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class RadioField extends SelectField
{
    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->cursorPresent = 'o';
    }
}