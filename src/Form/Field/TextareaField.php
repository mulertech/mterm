<?php

namespace MulerTech\MTerm\Form\Field;

class TextareaField extends TextField
{
    private int $rows = 3;

    public function setRows(int $rows): self
    {
        $this->rows = max(1, $rows);
        return $this;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function processInput($input)
    {
        if ($input === '') {
            return $this->defaultValue;
        }
        return $input;
    }
}