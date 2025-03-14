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
     * @param string $defaultValue
     * @return self
     */
    public function setDefault(string $defaultValue): self;

    /**
     * @return string|null
     */
    public function getDefault(): ?string;

    /**
     * @return bool
     */
    public function isMultipleInput(): bool;

    /**
     * @return bool
     */
    public function isMultipleSelection(): bool;

    /**
     * Process user input
     *
     * @param string $input Raw input from user
     * @return string|int|null|float Processed input value
     */
    public function processInput(string $input): string|int|null|float;

    /**
     * Validate field value
     *
     * @param string|null $value Field value to validate
     * @return array List of error messages (empty if valid)
     */
    public function validate(?string $value): array;
}