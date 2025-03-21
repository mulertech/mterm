<?php

namespace MulerTech\MTerm\Form\Field\Template;

use MulerTech\MTerm\Form\Field\SelectField;

/**
 * Class SelectSingleArrowTemplate
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class SelectSingleArrowTemplate extends SelectField
{
    public function __construct(string $name, string $label, bool $selectMultiple = false)
    {
        parent::__construct($name, $label, $selectMultiple);
        $this->setDescription('↑/↓: Navigation | ENTER: Selection');
        $this->cursorPresent = '>';
    }
}
