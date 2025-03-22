# MTerm

___
[![Latest Version on Packagist](https://img.shields.io/packagist/v/mulertech/mterm.svg?style=flat-square)](https://packagist.org/packages/mulertech/mterm)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mulertech/mterm/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mulertech/mterm/actions/workflows/tests.yml)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/mulertech/mterm/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/mulertech/mterm/actions/workflows/phpstan.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/mulertech/mterm.svg?style=flat-square)](https://packagist.org/packages/mulertech/mterm)
[![Test Coverage](https://raw.githubusercontent.com/mulertech/mterm/main/badge-coverage.svg)](https://packagist.org/packages/mulertech/mterm)
___

This class is a simple class to create a terminal interface for your application.

___

## Installation

###### _Two methods to install MTerm package with composer :_

1.

Add to your "**composer.json**" file into require section :

```
"mulertech/mterm": "^1.0"
```

and run the command :

```
php composer.phar update
```

2.

Run the command :

```
php composer.phar require mulertech/mterm "^1.0"
```

___

## Usage

MTerm provides a simple and elegant way to build interactive command-line interfaces in PHP. Below are the main classes and their methods with usage examples.

### Basic Usage

```php
$terminal = new Terminal();
$terminal->write('Hello, World!');
$terminal->writeLine('Hello with a new line!');
```

### Terminal Class

The main class for interacting with the terminal.

### Method Reference

Here's a comprehensive guide to all public methods in the Terminal class:

#### Reading Input

##### `read(string $prompt = null): string`

Reads a line of input from the terminal.

```php
$name = $terminal->read('Enter your name: ');
$input = $terminal->read(); // No prompt
```

##### `readChar(string $prompt = null): string`

Reads a single character from the terminal.

```php
$char = $terminal->readChar('Continue? (y/n): ');
if ($char === 'y') {
    // Process confirmation
}
```

#### Output Methods

##### `write(string $text, string $color = null, bool $bold = false): void`

Writes text to the terminal without a newline.

```php
$terminal->write('Regular text ');
$terminal->write('Red text ', 'red');
$terminal->write('Bold blue ', 'blue', true);
```

##### `writeLine(string $text, string $color = null, bool $bold = false): void`

Writes text to the terminal followed by a newline.

```php
$terminal->writeLine('First line');
$terminal->writeLine('Success message', 'green');
$terminal->writeLine('Error message', 'red', true);
```

#### Terminal Control

##### `clear(): void`

Clears the terminal screen.

```php
$terminal->clear();
```

##### `specialMode(): void`

Sets the terminal to special mode where characters are read immediately.

```php
$terminal->specialMode();
// Read characters without waiting for Enter
$terminal->normalMode(); // Return to standard mode
```

##### `normalMode(): void`

Restores the terminal to its normal mode.

```php
$terminal->normalMode();
```

##### `system(string $command): void`

Executes a system command.

```php
$terminal->system('ls -la');
```

#### Utility Methods

##### `supportsAnsi(): bool`

Checks if the terminal supports ANSI color codes.

```php
if ($terminal->supportsAnsi()) {
    $terminal->writeLine('Colors supported', 'green');
}
```

##### `inputStream(): resource`

Returns the input stream resource.

```php
$stream = $terminal->inputStream();
```

### Available Colors

The Terminal class supports: black, red, green, yellow, blue, magenta, cyan, white.

### Creating Interactive Menus

```php
function showMenu($terminal) {
    $terminal->clear();
    $terminal->writeLine('=== MENU ===', 'blue', true);
    $terminal->writeLine('1. Option One');
    $terminal->writeLine('2. Exit');
    return $terminal->read('Select: ');
}

while (true) {
    $choice = showMenu($terminal);
    if ($choice === '2') break;
}
```

## Command System

MTerm includes a robust command system for creating and managing terminal commands.

### CommandInterface

This interface defines the basic structure for all commands.

```php
class HelloCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'hello';
    }
    
    public function getDescription(): string
    {
        return 'Greets a user';
    }
    
    public function execute(array $args = []): int
    {
        $name = $args[0] ?? 'World';
        $this->terminal->writeLine("Hello, $name!");
        return 0;
    }
}
```

### AbstractCommand

A base class that implements basic functionality for CommandInterface.

```php
class DateCommand extends AbstractCommand
{
    public function __construct(Terminal $terminal) 
    {
        parent::__construct($terminal);
        $this->name = 'date';
        $this->description = 'Shows date/time';
    }
    
    public function execute(array $args = []): int
    {
        $format = $args[0] ?? 'Y-m-d H:i:s';
        $this->terminal->writeLine(date($format));
        return 0;
    }
}

// Usage
$cmd = new DateCommand($terminal);
$cmd->execute(['Y-m-d']); // Shows date in specified format
```

### CommandRegistry

Manages a collection of commands.

```php
$registry = new CommandRegistry();

$registry->register(new HelloCommand($terminal));
$registry->has('hello'); // Check if exists
$command = $registry->get('date'); // Get specific command
$allCommands = $registry->getAll(); // Get all commands
$registry->execute('hello', ['User']); // Execute with arguments
```

### Simple CLI Application

```php
$terminal = new Terminal();
$registry = new CommandRegistry();

// Register commands
$registry->register(new HelloCommand($terminal));

// Main loop
while (true) {
    $input = $terminal->read('> ');
    $parts = explode(' ', $input);
    $commandName = array_shift($parts);
    
    if ($commandName === 'exit') break;
    
    if ($registry->has($commandName)) {
        $registry->execute($commandName, $parts);
    }
}
```

## Application Class

The Application class implements a singleton pattern for managing terminal interactions.

### Method Reference

#### `getInstance(): Application`

Gets the singleton instance of the Application class.

```php
$app = Application::getInstance();
```

#### `getTerminal(): Terminal`

Returns the Terminal instance.

```php
$terminal = $app->getTerminal();
$terminal->writeLine('Hello!');
```

#### `getCommandRunner(): CommandRunner`

Returns the CommandRunner instance.

```php
$runner = $app->getCommandRunner();
$result = $runner->run('ls -la');
```

#### `run(): void`

Starts the application's main execution loop.

```php
$app = Application::getInstance();
$app->run();
```

## CommandRunner Class

The CommandRunner class provides methods to execute system commands.

### Method Reference

#### `run(string $command): array`

Executes a command and returns output and return code.

```php
$runner = new CommandRunner();
$result = $runner->run('echo "Hello"');
// Returns ['output' => ['Hello'], 'returnCode' => 0]
```

#### `runWithStderr(string $command): array`

Executes a command and returns stdout, stderr, and return code.

```php
$result = $runner->runWithStderr('ls /nonexistent');
// Returns ['stdout' => '', 'stderr' => 'error message...', 'returnCode' => 1]
```

### Combining Classes Example

```php
$app = Application::getInstance();
$terminal = $app->getTerminal();
$runner = $app->getCommandRunner();

$command = $terminal->read('Command: ');
$result = $runner->runWithStderr($command);
$terminal->writeLine($result['stdout']);
if ($result['stderr']) {
    $terminal->writeLine($result['stderr'], 'red');
}
```

## Form Classes

### AbstractField

#### `__construct(string $name, string $label)`

Constructor for the AbstractField class.

```php
use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\AbstractField;

$field = new AbstractField('username', 'Username');
$field->setDescription('Enter your username');
$field->setRequired(true);
$field->setDefault('guest');
$field->setTerminal(new Terminal());
```

#### `getName(): string`

Returns the name of the field.

```php
$field->getName(); // Returns 'username'
```

#### `getLabel(): string`

Returns the label of the field.

```php
$field->getLabel(); // Returns 'Username'
```

#### `getDescription(): ?string`

Returns the description of the field.

```php
$field->getDescription(); // Returns 'Enter your username'
```

#### `setDescription(string $description): self`

Sets the description of the field.

```php
$field->setDescription('Enter your username');
```

#### `isRequired(): bool`

Checks if the field is required.

```php
$field->isRequired(); // Returns true
```

#### `setRequired(bool $required = true): self`

Sets whether the field is required.

```php
$field->setRequired(true);
```

#### `getDefault(): string|int|float|array|null`

Returns the default value of the field.

```php
$field->getDefault(); // Returns 'guest'
```

#### `setDefault(string|int|float|array $defaultValue): self`

Sets the default value of the field.

```php
$field->setDefault('guest');
```

#### `clearErrors(): void`

Clears the errors of the field.

```php
$field->clearErrors();
```

#### `addValidator(ValidatorInterface $validator): self`

Adds a validator to the field.

```php
use MulerTech\MTerm\Validator\NotEmptyValidator;

$validator = new NotEmptyValidator();
$field->addValidator($validator);
```

#### `validate(string|int|float|array|null $value): array`

Validates the field value.

```php
$errors = $field->validate(''); // Returns array of errors
```

#### `processInput(string $input): string|int|float|array`

Processes the user input.

```php
$value = $field->processInput('guest'); // Returns processed value
```

#### `setTerminal(Terminal $terminal): self`

Sets the terminal instance for the field.

```php
$field->setTerminal(new Terminal());
```

### CheckboxField

#### `setCheckedValue(string $value): self`

Sets the checked value for the checkbox.

```php
use MulerTech\MTerm\Form\CheckboxField;

$field = new CheckboxField('accept_terms', 'Accept Terms');
$field->setCheckedValue('yes');
```

#### `setUncheckedValue(string $value): self`

Sets the unchecked value for the checkbox.

```php
$field->setUncheckedValue('no');
```

#### `processInput(string $input): string|int|float|array`

Processes the user input for the checkbox.

```php
$value = $field->processInput('yes'); // Returns 'yes'
```

### ColorField

#### `validate(string|int|float|array|null $value): array`

Validates the color field value.

```php
use MulerTech\MTerm\Form\ColorField;

$field = new ColorField('favorite_color', 'Favorite Color');
$errors = $field->validate('red'); // Returns array of errors
```

### DateField

#### `setFormat(string $format): self`

Sets the date format.

```php
use MulerTech\MTerm\Form\DateField;

$field = new DateField('birthdate', 'Birthdate');
$field->setFormat('Y-m-d');
```

#### `getFormat(): string`

Returns the date format.

```php
$field->getFormat(); // Returns 'Y-m-d'
```

#### `processInput(string $input): string|int|float|array`

Processes the user input for the date field.

```php
$value = $field->processInput('2022-01-01'); // Returns processed value
```

#### `validate(string|int|float|array|null $value): array`

Validates the date field value.

```php
$errors = $field->validate('2022-01-01'); // Returns array of errors
```

### EmailField

#### `validate(string|int|float|array|null $value): array`

Validates the email field value.

```php
use MulerTech\MTerm\Form\EmailField;

$field = new EmailField('email', 'Email');
$errors = $field->validate('user@example.com'); // Returns array of errors
```

### FieldInterface

#### `getName(): string`

Returns the name of the field.

#### `getLabel(): string`

Returns the label of the field.

#### `getDescription(): ?string`

Returns the description of the field.

#### `setDescription(string $description): self`

Sets the description of the field.

#### `isRequired(): bool`

Checks if the field is required.

#### `setRequired(bool $required = true): self`

Sets whether the field is required.

#### `clearErrors(): void`

Clears the errors of the field.

#### `setDefault(string|int|float|array $defaultValue): self`

Sets the default value of the field.

#### `getDefault(): string|int|float|array|null`

Returns the default value of the field.

#### `processInput(string $input): string|int|float|array`

Processes the user input.

#### `validate(string|int|float|array|null $value): array`

Validates the field value.

#### `setTerminal(Terminal $terminal): self`

Sets the terminal instance for the field.

### FileField

#### `setAllowedExtensions(array $extensions): self`

Sets the allowed file extensions.

```php
use MulerTech\MTerm\Form\FileField;

$field = new FileField('profile_picture', 'Profile Picture');
$field->setAllowedExtensions(['jpg', 'png']);
```

#### `setMaxSize(int $bytes): self`

Sets the maximum file size.

```php
$field->setMaxSize(1048576); // 1 MB
```

#### `processInput(string $input): string|int|float|array`

Processes the user input for the file field.

```php
$value = $field->processInput('path/to/file.jpg'); // Returns processed value
```

#### `validate(string|int|float|array|null $value): array`

Validates the file field value.

```php
$errors = $field->validate('path/to/file.jpg'); // Returns array of errors
```

### NumberField

#### `setMin(?float $min): self`

Sets the minimum value for the number field.

```php
use MulerTech\MTerm\Form\NumberField;

$field = new NumberField('age', 'Age');
$field->setMin(18);
```

#### `setMax(?float $max): self`

Sets the maximum value for the number field.

```php
$field->setMax(99);
```

#### `setAllowFloat(bool $allowFloat): self`

Sets whether to allow floating-point numbers.

```php
$field->setAllowFloat(false);
```

#### `processInput(string $input): string|int|float|array`

Processes the user input for the number field.

```php
$value = $field->processInput('25'); // Returns processed value
```

#### `validate(string|int|float|array|null $value): array`

Validates the number field value.

```php
$errors = $field->validate('25'); // Returns array of errors
```

### PasswordField

#### `isMaskInput(): bool`

Checks if the input should be masked.

```php
use MulerTech\MTerm\Form\PasswordField;

$field = new PasswordField('password', 'Password');
$field->isMaskInput(); // Returns true
```

#### `setMaskInput(bool $maskInput = true): self`

Sets whether the input should be masked.

```php
$field->setMaskInput(true);
```

#### `getMaskChar(): string`

Returns the mask character.

```php
$field->getMaskChar(); // Returns '*'
```

#### `setMaskChar(string $maskChar): self`

Sets the mask character.

```php
$field->setMaskChar('*');
```

#### `parseInput(string $input): string`

Parses the user input.

```php
$value = $field->parseInput('password'); // Returns parsed value
```

#### `processInput(string $input = ''): string`

Processes the user input for the password field.

```php
$value = $field->processInput('password'); // Returns processed value
```

### RadioField

#### `__construct(string $name, string $label)`

Constructor for the RadioField class.

```php
use MulerTech\MTerm\Form\RadioField;

$field = new RadioField('gender', 'Gender');
```

#### `setMultipleSelection(bool $multipleSelection = true): self`

Sets whether multiple selection is allowed.

```php
$field->setMultipleSelection(false);
```

### RangeField

#### `__construct(string $name, string $label)`

Constructor for the RangeField class.

```php
use MulerTech\MTerm\Form\RangeField;

$field = new RangeField('rating', 'Rating');
```

#### `setStep(int $step): self`

Sets the step value for the range field.

```php
$field->setStep(1);
```

#### `getStep(): int`

Returns the step value for the range field.

```php
$field->getStep(); // Returns 1
```

#### `validate(string|int|float|array|null $value): array`

Validates the range field value.

```php
$errors = $field->validate(5); // Returns array of errors
```

### SelectField

#### `__construct(string $name, string $label, bool $multipleSelection = false)`

Constructor for the SelectField class.

```php
use MulerTech\MTerm\Form\SelectField;

$field = new SelectField('country', 'Country');
```

#### `setOptions(array $options): self`

Sets the options for the select field.

```php
$field->setOptions(['USA', 'Canada', 'UK']);
```

#### `setMultipleSelection(bool $multipleSelection = true): self`

Sets whether multiple selection is allowed.

```php
$field->setMultipleSelection(false);
```

#### `isMultipleSelection(): bool`

Checks if multiple selection is allowed.

```php
$field->isMultipleSelection(); // Returns false
```

#### `parseInput(string $input): string`

Parses the user input.

```php
$value = $field->parseInput('USA'); // Returns parsed value
```

#### `processInput(string $input = ''): string|array`

Processes the user input for the select field.

```php
$value = $field->processInput('USA'); // Returns processed value
```

#### `renderSelectMultipleField(Terminal $terminal): array`

Renders the select field for multiple selection.

```php
$options = $field->renderSelectMultipleField(new Terminal()); // Returns array of options
```

#### `renderSelectSingleField(Terminal $terminal): string`

Renders the select field for single selection.

```php
$option = $field->renderSelectSingleField(new Terminal()); // Returns selected option
```

#### `validate(string|int|float|array|null $value): array`

Validates the select field value.

```php
$errors = $field->validate('USA'); // Returns array of errors
```

#### `getCurrentOption(): string`

Returns the current selected option.

```php
$field->getCurrentOption(); // Returns 'USA'
```

### TextField

#### `setMinLength(int $minLength): self`

Sets the minimum length for the text field.

```php
use MulerTech\MTerm\Form\TextField;

$field = new TextField('username', 'Username');
$field->setMinLength(3);
```

#### `setMaxLength(?int $maxLength): self`

Sets the maximum length for the text field.

```php
$field->setMaxLength(20);
```

#### `validate(string|int|float|array|null $value): array`

Validates the text field value.

```php
$errors = $field->validate('guest'); // Returns array of errors
```

### UrlField

#### `validate(string|int|float|array|null $value): array`

Validates the URL field value.

```php
use MulerTech\MTerm\Form\UrlField;

$field = new UrlField('website', 'Website');
$errors = $field->validate('https://example.com'); // Returns array of errors
```

### Form

#### `__construct(Terminal $terminal)`

Constructor for the Form class.

```php
use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Form;

$form = new Form(new Terminal());
```

#### `addField(FieldInterface $field): self`

Adds a field to the form.

```php
use MulerTech\MTerm\Form\TextField;

$field = new TextField('username', 'Username');
$form->addField($field);
```

#### `handle(): void`

Handles the form submission.

```php
$form->handle();
```

#### `isSubmitted(): bool`

Checks if the form has been submitted.

```php
$form->isSubmitted(); // Returns true or false
```

#### `isValid(): bool`

Checks if the form is valid.

```php
$form->isValid(); // Returns true or false
```

#### `getValues(): array`

Returns all form values.

```php
$values = $form->getValues(); // Returns array of values
```

#### `getValue(string $fieldName): string|int|float|array|null`

Returns a specific form value.

```php
$value = $form->getValue('username'); // Returns value of 'username' field
```

### FormRenderer

#### `__construct(Terminal $terminal)`

Constructor for the FormRenderer class.

```php
use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\FormRenderer;

$renderer = new FormRenderer(new Terminal());
```

#### `renderField(FieldInterface $field): string|array`

Renders a field.

```php
use MulerTech\MTerm\Form\TextField;

$field = new TextField('username', 'Username');
$output = $renderer->renderField($field); // Returns rendered field
```

#### `renderErrors(array $errors): void`

Renders the errors.

```php
$renderer->renderErrors(['Error 1', 'Error 2']);
```

#### `clear(): void`

Clears the terminal screen.

```php
$renderer->clear();
```

### ValidatorInterface

#### `validate(mixed $value): ?string`

Validates a value.

```php
use MulerTech\MTerm\Validator\ValidatorInterface;

class CustomValidator implements ValidatorInterface
{
    public function validate($value): ?string
    {
        return $value === 'valid' ? null : 'Invalid value';
    }
}
```

### AbstractValidator

#### `__construct(string $errorMessage)`

Constructor for the AbstractValidator class.

```php
use MulerTech\MTerm\Validator\AbstractValidator;

class CustomValidator extends AbstractValidator
{
    public function __construct()
    {
        parent::__construct('Invalid value');
    }
    
    public function validate($value): ?string
    {
        return $value === 'valid' ? null : $this->getErrorMessage();
    }
}
```

#### `getErrorMessage(): string`

Returns the error message.

```php
$validator = new CustomValidator();
$errorMessage = $validator->getErrorMessage(); // Returns 'Invalid value'
```

### ChoiceValidator

#### `__construct(array $choices, bool $strict = true, string $errorMessage = "Selected value is not a valid choice.")`

Constructor for the ChoiceValidator class.

```php
use MulerTech\MTerm\Validator\ChoiceValidator;

$validator = new ChoiceValidator(['option1', 'option2']);
```

#### `validate(mixed $value): ?string`

Validates a value.

```php
$error = $validator->validate('option1'); // Returns null (valid)
$error = $validator->validate('invalid'); // Returns error message (invalid)
```

### DateValidator

#### `__construct(string $format = 'Y-m-d', ?DateTimeInterface $minDate = null, ?DateTimeInterface $maxDate = null, ?string $errorMessage = null)`

Constructor for the DateValidator class.

```php
use MulerTech\MTerm\Validator\DateValidator;

$validator = new DateValidator('Y-m-d');
```

#### `validate(mixed $value): ?string`

Validates a value.

```php
$error = $validator->validate('2022-01-01'); // Returns null (valid)
$error = $validator->validate('invalid-date'); // Returns error message (invalid)
```

### EmailValidator

#### `__construct(string $errorMessage = "Please enter a valid email address.")`

Constructor for the EmailValidator class.

```php
use MulerTech\MTerm\Validator\EmailValidator;

$validator = new EmailValidator();
```

#### `validate(mixed $value): ?string`

Validates a value.

```php
$error = $validator->validate('user@example.com'); // Returns null (valid)
$error = $validator->validate('invalid-email'); // Returns error message (invalid)
```

### IpAddressValidator

#### `__construct(bool $allowIPv4 = true, bool $allowIPv6 = true, bool $allowPrivate = true, bool $allowReserved = true, string $errorMessage = "Please enter a valid IP address.")`

Constructor for the IpAddressValidator class.

```php
use MulerTech\MTerm\Validator\IpAddressValidator;

$validator = new IpAddressValidator();
```

#### `validate(mixed $value): ?string`

Validates a value.

```php
$error = $validator->validate('192.168.0.1'); // Returns null (valid)
$error = $validator->validate('invalid-ip'); // Returns error message (invalid)
```

### LengthValidator

#### `__construct(?int $min = null, ?int $max = null, ?string $errorMessage = null)`

Constructor for the LengthValidator class.

```php
use MulerTech\MTerm\Validator\LengthValidator;

$validator = new LengthValidator(3, 20);
```

#### `validate(mixed $value): ?string`

Validates a value.

```php
$error = $validator->validate('valid'); // Returns null (valid)
$error = $validator->validate(''); // Returns error message (invalid)
```

### NotEmptyValidator

#### `__construct(string $errorMessage = "This value cannot be empty.")`

Constructor for the NotEmptyValidator class.

```php
use MulerTech\MTerm\Validator\NotEmptyValidator;

$validator = new NotEmptyValidator();
```

#### `validate(mixed $value): ?string`

Validates a value.

```php
$error = $validator->validate('valid'); // Returns null (valid)
$error = $validator->validate(''); // Returns error message (invalid)
```

### NumericRangeValidator

#### `__construct(?float $min = null, ?float $max = null, ?string $errorMessage = null)`

Constructor for the NumericRangeValidator class.

```php
use MulerTech\MTerm\Validator\NumericRangeValidator;

$validator = new NumericRangeValidator(1, 100);
```

#### `validate(mixed $value): ?string`

Validates a value.

```php
$error = $validator->validate(50); // Returns null (valid)
$error = $validator->validate(200); // Returns error message (invalid)
```

### PatternValidator

#### `__construct(string $pattern, string $errorMessage = "Value does not match required pattern.")`

Constructor for the PatternValidator class.

```php
use MulerTech\MTerm\Validator\PatternValidator;

$validator = new PatternValidator('/^[a-z]+$/');
```

#### `validate(mixed $value): ?string`

Validates a value.

```php
$error = $validator->validate('valid'); // Returns null (valid)
$error = $validator->validate('123'); // Returns error message (invalid)
```

### RegexValidator

#### `__construct(string $pattern, string $errorMessage = "This value is not valid.")`

Constructor for the RegexValidator class.

```php
use MulerTech\MTerm\Validator\RegexValidator;

$validator = new RegexValidator('/^[a-z]+$/');
```

#### `validate(mixed $value): ?string`

Validates a value.

```php
$error = $validator->validate('valid'); // Returns null (valid)
$error = $validator->validate('123'); // Returns error message (invalid)
```

## Utils Classes

### ProgressBar

#### `__construct(Terminal $terminal, int $total = 100, int $width = 50, string $completeChar = '=', string $incompleteChar = '-', string $color = Terminal::COLORS['green'])`

Constructor for the ProgressBar class.

```php
use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Utils\ProgressBar;

$terminal = new Terminal();
$progressBar = new ProgressBar($terminal, 100, 50, '=', '-', 'green');
```

#### `start(): void`

Starts the progress bar.

```php
$progressBar->start();
```

#### `advance(int $step = 1): void`

Advances the progress bar by a specific amount.

```php
$progressBar->advance(10);
```

#### `setProgress(int $current): void`

Sets the progress to a specific value.

```php
$progressBar->setProgress(50);
```

#### `finish(): void`

Finishes the progress bar.

```php
$progressBar->finish();
```

### TableFormatter

#### `__construct(Terminal $terminal, string $headerColor = Terminal::COLORS['green'], string $borderColor = Terminal::COLORS['blue'], string $cellColor = Terminal::COLORS['white'], int $padding = 1)`

Constructor for the TableFormatter class.

```php
use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Utils\TableFormatter;

$terminal = new Terminal();
$tableFormatter = new TableFormatter($terminal, 'green', 'blue', 'white', 1);
```

#### `renderTable(array $headers, array $rows): void`

Formats and renders a table.

```php
$headers = ['Name', 'Age', 'Country'];
$rows = [
    ['John', 25, 'USA'],
    ['Jane', 30, 'Canada'],
    ['Doe', 22, 'UK']
];

$tableFormatter->renderTable($headers, $rows);
```
