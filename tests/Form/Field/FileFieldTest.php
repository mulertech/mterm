<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\FileField;
use PHPUnit\Framework\TestCase;

class FileFieldTest extends TestCase
{
    private FileField $field;
    private string $testFilePath;
    private string $tempDir;

    protected function setUp(): void
    {
        $this->field = new FileField('file', 'File Field');

        // Create a temporary file for testing
        $this->tempDir = sys_get_temp_dir();
        $this->testFilePath = $this->tempDir . DIRECTORY_SEPARATOR . 'test_file.txt';
        file_put_contents($this->testFilePath, str_repeat('a', 1024)); // 1KB test file
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }
    }

    public function testSetAllowedExtensions(): void
    {
        $result = $this->field->setAllowedExtensions(['txt', 'pdf']);
        $this->assertSame($this->field, $result);

        // Create a file with invalid extension
        $invalidFile = $this->tempDir . DIRECTORY_SEPARATOR . 'test_file.jpg';
        file_put_contents($invalidFile, 'test content');

        $errors = $this->field->validate($invalidFile);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Allowed types: txt, pdf', $errors[0]);

        unlink($invalidFile);
    }

    public function testSetMaxSize(): void
    {
        $result = $this->field->setMaxSize(512);
        $this->assertSame($this->field, $result);

        $errors = $this->field->validate($this->testFilePath);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('File is too large', $errors[0]);
    }

    public function testProcessInputWithEmptyInput(): void
    {
        $this->field->setDefault('default_value.txt');
        $result = $this->field->processInput('');
        $this->assertEquals('default_value.txt', $result);
    }

    public function testProcessInputWithNonEmptyInput(): void
    {
        $result = $this->field->processInput('my_file.txt');
        $this->assertEquals('my_file.txt', $result);
    }

    public function testValidateFileNotFound(): void
    {

        $errors = $this->field->validate(
            DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'non_existent_file.txt'
        );
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('File not found', $errors[0]);
    }

    public function testValidateWithAllowedExtension(): void
    {
        $this->field->setAllowedExtensions(['txt']);

        $errors = $this->field->validate($this->testFilePath);
        $this->assertEmpty($errors);
    }

    public function testValidateWithDisallowedExtension(): void
    {
        $this->field->setAllowedExtensions(['pdf', 'doc']);

        $errors = $this->field->validate($this->testFilePath);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('File type not allowed', $errors[0]);
    }

    public function testValidateWithSizeBelowMax(): void
    {
        $this->field->setMaxSize(2048); // 2KB

        $errors = $this->field->validate($this->testFilePath);
        $this->assertEmpty($errors);
    }

    public function testValidateWithSizeAboveMax(): void
    {
        $this->field->setMaxSize(512); // 0.5KB

        $errors = $this->field->validate($this->testFilePath);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('File is too large', $errors[0]);
    }

    public function testValidateWithMultipleConstraints(): void
    {
        $this->field->setAllowedExtensions(['pdf'])->setMaxSize(512);
        $errors = $this->field->validate($this->testFilePath);
        $this->assertCount(2, $errors);
    }

    public function testValidateWithNullValue(): void
    {
        $this->field->setRequired(false);
        $errors = $this->field->validate(null);
        $this->assertEmpty($errors);

        $this->field->setRequired();
        $errors = $this->field->validate(null);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);
    }

    public function testValidateWithEmptyString(): void
    {
        $this->field->setRequired(false);
        $errors = $this->field->validate('');
        $this->assertEmpty($errors);

        $this->field->setRequired();
        $errors = $this->field->validate('');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);
    }

    public function testExtensionsAreCaseInsensitive(): void
    {
        $this->field->setAllowedExtensions(['TXT', 'PDF']);

        $errors = $this->field->validate($this->testFilePath);
        $this->assertEmpty($errors);
    }
}