<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Interface FieldInterface
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
interface FieldInterface
{
    /**
     * Get field name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get field label
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get field description
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self;

    /**
     * Check if field is required
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * @param bool $required
     * @return self
     */
    public function setRequired(bool $required = true): self;

    /**
     * @return array<string>
     */
    public function getErrors(): array;

    /**
     * @return void
     */
    public function clearErrors(): void;

    /**
     * @param string|int|float|array<string> $defaultValue
     * @return self
     */
    public function setDefault(string|int|float|array $defaultValue): self;

    /**
     * @return string|int|float|array<string>|null
     */
    public function getDefault(): string|int|float|array|null;

    /**
     * @return bool
     */
    public function isMultipleInput(): bool;

    /**
     * Process user input
     *
     * @param string $input Raw input from user
     * @return string|int|float|array<int|string, string> Processed input value
     */
    public function processInput(string $input): string|int|float|array;

    /**
     * Validate field value
     *
     * @param string|int|float|array<int|string, string>|null $value Field value to validate
     * @return array<string> List of error messages (empty if valid)
     */
    public function validate(string|int|float|array|null $value): array;
}
