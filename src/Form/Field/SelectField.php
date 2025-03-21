<?php

namespace MulerTech\MTerm\Form\Field;

use MulerTech\MTerm\Core\Terminal;

/**
 * Class SelectField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class SelectField extends AbstractField
{
    /**
     * @var array<int|string, string> $options
     */
    protected array $options = [];
    protected bool $multipleSelection = false;
    protected string $checkboxUnchecked = '[ ]';
    protected string $checkboxChecked = '[X]';
    protected string $cursorAbsent = ' ';
    protected string $cursorPresent = '*';
    private int $cursorPosition = 0;
    /**
     * @var array<int|string, string> $selectedOptions
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
     * @param array<int|string, string> $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param bool $multipleSelection
     * @return $this
     */
    public function setMultipleSelection(bool $multipleSelection = true): self
    {
        $this->multipleSelection = $multipleSelection;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMultipleSelection(): bool
    {
        return $this->multipleSelection;
    }

    /**
     * @param string $input
     * @return string
     */
    public function parseInput(string $input): string
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
     * Process user input for this field
     *
     * @return string|array<int|string, string>
     */
    public function processInput(string $input = ''): string|array
    {
        $terminal = $this->terminal;

        if ($terminal === null) {
            throw new \RuntimeException('Terminal must be set before calling processInput');
        }

        $this->clearErrors();

        if ($this->isMultipleSelection()) {
            return $this->renderSelectMultipleField($terminal);
        }

        return $this->renderSelectSingleField($terminal);
    }

    /**
     * @return array<int|string, string>
     */
    public function renderSelectMultipleField(Terminal $terminal): array
    {
        $this->clearErrors();

        $result = $this->handleSelectField($terminal);

        if ($result === true && $this->selectedOptions !== []) {
            return $this->selectedOptions;
        }

        $defaultValue = $this->getDefault() ?? [];

        return is_array($defaultValue) ? $defaultValue : [];
    }

    /**
     * @return string
     */
    public function renderSelectSingleField(Terminal $terminal): string
    {
        $this->clearErrors();

        $result = $this->handleSelectField($terminal);

        $defaultValue = $this->getDefault() ?? '';
        $defaultValue = is_string($defaultValue) ? $defaultValue : '';

        return $result === true ? $this->getCurrentOption() : $defaultValue;
    }

    /**
     * @return bool
     */
    private function handleSelectField(Terminal $terminal): bool
    {
        $prompt = $this->buildPrompt();
        $header = $prompt . PHP_EOL;

        if ($this->getDescription() !== null) {
            $header .= $this->getDescription() . PHP_EOL;
        }

        $terminal->specialMode();
        $terminal->write($header, 'cyan');
        $terminal->write($this->parseInput(''));

        $result = $this->handleSelectKeyboardInput($header, $terminal);

        $terminal->normalMode();
        return $result;
    }

    /**
     * @param string $header
     * @return bool
     */
    private function handleSelectKeyboardInput(string $header, Terminal $terminal): bool
    {
        while (true) {
            $char = $terminal->readChar();

            if ($char === PHP_EOL) { // Enter key
                return true;
            }

            if ($char === "\033") {
                $this->handleArrowKey($header, $terminal);
                continue;
            }

            if ($char === ' ') {
                $terminal->clear();
                $terminal->write($header, 'cyan');
                $terminal->write($this->parseInput('space'));
            }

            if ($char === 'a') {
                $terminal->clear();
                $terminal->write($header, 'cyan');
                $terminal->write($this->parseInput('a'));
            }
        }
    }

    /**
     * @param string $header
     * @return void
     */
    private function handleArrowKey(string $header, Terminal $terminal): void
    {
        $sequence = $terminal->readChar() . $terminal->readChar();

        if ($sequence === "[A") { // Up arrow
            $terminal->clear();
            $terminal->write($header, 'cyan');
            $terminal->write($this->parseInput('up'));
        } elseif ($sequence === "[B") { // Down arrow
            $terminal->clear();
            $terminal->write($header, 'cyan');
            $terminal->write($header, 'cyan');
            $terminal->write($this->parseInput('down'));
        }
    }

    /**
     * @param string|int|float|array<int|string, string>|null $value
     * @return array<string>
     */
    public function validate(string|int|float|array|null $value): array
    {
        $this->errors = parent::validate($value);

        if (empty($value) || is_numeric($value)) {
            return $this->errors;
        }

        if ($this->multipleSelection && is_array($value)) {
            foreach ($value as $key => $val) {
                if (!array_key_exists($key, $this->options)) {
                    $this->errors[] = "Invalid option: '$key'.";
                }
            }

            return $this->errors;
        }

        if (!is_array($value) && !array_key_exists($value, $this->options)) {
            $this->errors[] = "Invalid option: '$value'.";
        }

        return $this->errors;
    }

    /**
     * Get the current selected option key
     *
     * @return string
     */
    public function getCurrentOption(): string
    {
        $options = array_keys($this->options);
        return (string)($options[$this->cursorPosition] ?? '');
    }

    /**
     * @return string
     */
    private function buildPrompt(): string
    {
        $label = $this->getLabel();
        $required = $this->isRequired() ? ' (required)' : '';
        return "$label$required: ";
    }

    /**
     * Generate the select display
     *
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
