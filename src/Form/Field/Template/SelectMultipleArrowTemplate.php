<?php

namespace MulerTech\MTerm\Form\Field\Template;

use MulerTech\MTerm\Form\Field\SelectField;

/**
 * Class SelectSingleArrowTemplate.
 *
 * @author Sébastien Muler
 */
class SelectMultipleArrowTemplate extends SelectField
{
    public function __construct(string $name, string $label, bool $selectMultiple = true)
    {
        parent::__construct($name, $label, $selectMultiple);
        $this->setDescription('↑/↓: Navigation | SPACE: Selection | ENTER: Confirm | a: All');
        $this->checkboxChecked = '[✓]';
        $this->cursorPresent = '>';
    }
}
