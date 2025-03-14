<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class SelectField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class SelectField extends AbstractField
{
    /**
     * @var array<int, string> $options
     */
    protected array $options = [];
    protected string $checkboxUnchecked = '[ ]';
    protected string $checkboxChecked = '[X]';
    protected string $cursorAbsent = ' ';
    protected string $cursorPresent = '*';
    private int $cursorPosition = 0;
    /**
     * @var array<int, string> $selectedOptions
     */
    protected array $selectedOptions = [];

    /**
     * @param string $name
     * @param string $label
     * @param bool $multipleSelection
     */
    public function __construct(string $name, string $label, bool $multipleSelection = false)
    {
        parent::__construct($name, $label);
        $this->multipleInput = true;
        $this->multipleSelection = $multipleSelection;
    }

    /**
     * @param array<int, string> $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param string $input
     * @return string|int|float|null
     */
    public function processInput(string $input): string|int|null|float
    {
        // Generate the selected options
        if ($this->multipleSelection) {
            // Update the position of the cursor
            if ($input === 'up') {
                $this->cursorPosition = max(0, $this->cursorPosition - 1);
            } elseif ($input === 'down') {
                $this->cursorPosition = min(count($this->options) - 1, $this->cursorPosition + 1);
            } elseif ($input === 'space') {
                $key = array_keys($this->options)[$this->cursorPosition];
                if (isset($this->selectedOptions[$key])) {
                    unset($this->selectedOptions[$key]);
                } else {
                    $this->selectedOptions[$key] = $this->options[$key];
                }
            } elseif ($input === 'a') {
                $this->selectedOptions = $this->options;
            }

            return $this->processSelect(true);
        }

        // Update the position of the cursor
        if ($input === 'up') {
            $this->cursorPosition = max(0, $this->cursorPosition - 1);
        } elseif ($input === 'down') {
            $this->cursorPosition = min(count($this->options) - 1, $this->cursorPosition + 1);
        }

        return $this->processSelect();
    }

    /**
     * @param string|null $value
     * @return array
     */
    public function validate(?string $value): array
    {
        $errors = parent::validate($value);

        if ($value === null || $value === '') {
            return $errors;
        }

        if ($this->multipleSelection) {
            foreach (explode(',', $value) as $val) {
                if (!array_key_exists($val, $this->options)) {
                    $errors[] = "Invalid option: '$val'.";
                }
            }

            return $errors;
        }

        if (!array_key_exists($value, $this->options)) {
            $errors[] = "Invalid option: '$value'.";
        }

        return $errors;
    }

    /**
     * @return array<int, string>
     */
    public function getSelectedOptions(): array
    {
        return $this->selectedOptions;
    }

    /**
     * @return string
     */
    public function getPlainSelectedOptions(): string
    {
        $options = array_keys($this->options);
        $selected = array_keys($this->selectedOptions);
        return implode(',', array_intersect($options, $selected));
    }

    /**
     * @return int|string
     */
    public function getCurrentOption(): int|string
    {
        $options = array_keys($this->options);
        return $options[$this->cursorPosition] ?? '';
    }

    /**
     * @param bool $multiple
     * @return string
     */
    private function processSelect(bool $multiple = false): string
    {
        $echo = '';
        $selected = '';
        $position = 0;

        foreach ($this->options as $key => $value) {
            if ($multiple) {
                $selected = isset($this->selectedOptions[$key]) ? $this->checkboxChecked : $this->checkboxUnchecked;
                $selected .= ' ';
            }
            $cursor = ($position++ === $this->cursorPosition) ? $this->cursorPresent : $this->cursorAbsent;
            $echo .= "$cursor $selected $value" . PHP_EOL;
        }

        return $echo;
    }
}