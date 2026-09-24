# MTerm

___
[![Latest Version on Packagist](https://img.shields.io/packagist/v/mulertech/mterm.svg?style=flat-square)](https://packagist.org/packages/mulertech/mterm)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mulertech/mterm/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mulertech/mterm/actions/workflows/tests.yml)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/mulertech/mterm/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/mulertech/mterm/actions/workflows/phpstan.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/mulertech/mterm.svg?style=flat-square)](https://packagist.org/packages/mulertech/mterm)
[![Test Coverage](https://raw.githubusercontent.com/mulertech/mterm/badge/badge-coverage.svg)](https://packagist.org/packages/mulertech/mterm)
___

This class is a simple class to create a terminal interface for your application.

___

## Installation

###### _Two methods to install MTerm package with composer :_

1.

Add to your "**composer.json**" file into require section :

```
"mulertech/mterm": "^2.0"
```

and run the command :

```
php composer.phar update
```

2.

Run the command :

```
php composer.phar require mulertech/mterm "^2.0"
```

___

## Usage

MTerm provides a simple and elegant way to build interactive command-line interfaces in PHP. Below are the main classes and their methods with usage examples.

### Basic Usage

```php
use MulerTech\MTerm\Core\Color;
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();
$terminal->write('Hello, World!');
$terminal->writeLine('Hello with a new line!', Color::Green);
```

### Terminal Class

The main class for interacting with the terminal. It writes through an output
and reads through an input reader, both replaceable:

```php
use MulerTech\MTerm\Core\Input\InputReader;
use MulerTech\MTerm\Core\Output\StreamOutput;
use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Core\TerminalMode;

$terminal = new Terminal();                                  // standard streams
$terminal = new Terminal(new StreamOutput($file));           // into a file
$terminal = new Terminal(new StreamOutput(), new InputReader($stream), new TerminalMode());
```

Escape sequences are written only when the output is a terminal that accepts
them, so a display redirected to a file carries text alone.

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

Reads one whole character, however many bytes it takes.

```php
$char = $terminal->readChar('Continue? (y/n): ');
if ($char === 'y') {
    // Process confirmation
}
```

##### `readKey(string $prompt = null): KeyPress`

Reads one key press. An escape sequence — an arrow, a function key — comes back
as the key it names rather than as its bytes.

```php
use MulerTech\MTerm\Core\Input\Key;

$press = $terminal->readKey();

if ($press->is(Key::Up)) {
    // Move the cursor up
}

if ($press->isCharacter()) {
    $typed = $press->character;
}

if ($press->isEndOfInput()) {
    // Nothing left to read
}
```

#### Output Methods

##### `write(string $text, Color $color = null, bool $bold = false): void`

Writes text to the terminal without a newline.

```php
$terminal->write('Regular text ');
$terminal->write('Red text ', Color::Red);
$terminal->write('Bold blue ', Color::Blue, true);
```

##### `writeLine(string $text = '', Color $color = null, bool $bold = false): void`

Writes text to the terminal followed by a newline.

```php
$terminal->writeLine('First line');
$terminal->writeLine('Success message', Color::Green);
$terminal->writeLine('Error message', Color::Red, true);
$terminal->writeLine(); // An empty line
```

##### `getOutput(): OutputInterface` and `getInput(): InputReader`

Return the output and the input reader the terminal was built with.

#### Terminal Control

Screen and cursor are driven by ANSI sequences, without forking a subprocess.

##### `clear(): void`

Erases the screen and its scrollback, and puts the cursor back at its top left corner. The
scrollback goes too: some terminals — PhpStorm's among them — push an erased screen into the
scrollback, and the last line of what came before would stay visible above what is drawn next.

```php
$terminal->clear();
```

##### `clearLine(): void`

Erases the line the cursor sits on, and returns to its first column.

```php
$terminal->clearLine();
$terminal->write('Replaced content');
```

##### `moveCursor(int $row, int $column): void`

Places the cursor, counting rows and columns from one.

```php
$terminal->moveCursor(1, 1); // Top left corner
```

##### `hideCursor(): void` and `showCursor(): void`

Hide the cursor while a page is being repainted, and show it again.

```php
$terminal->hideCursor();
$terminal->showCursor();
```

##### `enableRawMode(): void`

Reads keys one by one, without echo. The previous state is saved and restored
by a shutdown handler as well as by the signal handlers, so an exception or an
interruption never leaves the terminal without echo. Catching the interruption
needs `ext-pcntl`; without it only the shutdown handler stands.

```php
$terminal->enableRawMode();
$press = $terminal->readKey();
$terminal->disableRawMode();
```

##### `disableRawMode(): void` and `isRawMode(): bool`

Put the terminal back as it was found, and tell whether it is in raw mode.

#### Utility Methods

##### `supportsAnsi(): bool`

Tells whether escape sequences written to the output are interpreted. It answers
false when `NO_COLOR` is set, and when the output is not a terminal.

```php
if ($terminal->supportsAnsi()) {
    $terminal->writeLine('Colors supported', Color::Green);
}
```

### Color

The eight colors every ANSI terminal renders: `Color::Black`, `Color::Red`,
`Color::Green`, `Color::Yellow`, `Color::Blue`, `Color::Magenta`, `Color::Cyan`,
`Color::White`. Passing `true` as the third argument of `write()` and
`writeLine()` makes the text bold.

```php
Color::Green->sequence();     // "\033[0;32m"
Color::Green->sequence(true); // "\033[1;32m"
Color::RESET;                 // "\033[0m"
```

### Output Classes

#### `OutputInterface`

Destination of everything the library displays: `write(string $text): void` and
`isDecorated(): bool`.

#### `StreamOutput`

Writes to a stream, the standard output unless another one is given. Decoration
is detected — `NO_COLOR`, a stream that is not a terminal — or forced:

```php
$output = new StreamOutput();                       // Standard output
$output = new StreamOutput(fopen('report.txt', 'w')); // No escape sequence written
$output = new StreamOutput(STDOUT, true);           // Decoration forced on
```

#### `BufferedOutput`

Keeps everything in memory, which is how a display is asserted upon in a test.

```php
$output = new BufferedOutput();
$terminal = new Terminal($output);
$terminal->writeLine('Hello');

$output->content(); // "Hello\n"
$output->fetch();   // "Hello\n", and empties the buffer
```

### Input Classes

#### `InputReader`

Reads a stream by press rather than by byte: `readLine()`, `readCharacter()` —
one whole character, accents and emoji included — and `readKey()`.

#### `Key`

The keys that carry no printable character of their own: `Up`, `Down`, `Right`,
`Left`, `Enter`, `Escape`, `Backspace`, `Delete`, `Insert`, `Tab`, `Space`,
`Home`, `End`, `PageUp`, `PageDown` and `F1` to `F12`.

#### `KeyPress`

One press: `is(Key $key)`, `isCharacter()`, `isEndOfInput()`, and the `key` and
`character` it carries.

### TerminalMode

Raw mode and the guarantee that the terminal comes back from it. `Terminal`
drives it; it is only built directly to be replaced in a test.

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

#### `runDirect(string $command): int`

Executes a command that writes to the terminal itself, and returns its exit
code. Deployment logs and test suites are watched as they unfold; capturing
their output to display it afterwards would trade that for a tidier summary.

```php
$returnCode = $runner->runDirect('docker compose logs -f');
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
    $terminal->writeLine($result['stderr'], Color::Red);
}
```

## Form Classes

### AbstractField

#### `__construct(string $name, string $label)`

Constructor for the AbstractField class.

```php
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
$field = new ColorField('favorite_color', 'Favorite Color');
$errors = $field->validate('red'); // Returns array of errors
```

### DateField

#### `setFormat(string $format): self`

Sets the date format.

```php
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
$field = new UrlField('website', 'Website');
$errors = $field->validate('https://example.com'); // Returns array of errors
```

### Form

#### `__construct(Terminal $terminal)`

Constructor for the Form class.

```php
$form = new Form(new Terminal());
```

#### `addField(FieldInterface $field): self`

Adds a field to the form.

```php
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
$renderer = new FormRenderer(new Terminal());
```

#### `renderField(FieldInterface $field): string|array`

Renders a field.

```php
$field = new TextField('username', 'Username');
$output = $renderer->renderField($field); // Returns rendered field
```

#### `renderErrors(array $errors): void`

Renders the errors.

```php
$renderer->renderErrors(['Error 1', 'Error 2']);
```

#### `clear(): void`

Clears the terminal screen and its scrollback, through `Terminal::clear()`.

```php
$renderer->clear();
```

### ValidatorInterface

#### `validate(mixed $value): ?string`

Validates a value.

```php
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

#### `__construct(Terminal $terminal, int $total = 100, int $width = 50, string $completeChar = '=', string $incompleteChar = '-', Color $color = Color::Green)`

Constructor for the ProgressBar class.

```php
$terminal = new Terminal();
$progressBar = new ProgressBar($terminal, 100, 50, '=', '-', Color::Green);
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

#### `__construct(Terminal $terminal, Color $headerColor = Color::Green, Color $borderColor = Color::Blue, Color $cellColor = Color::White, int $padding = 1)`

Constructor for the TableFormatter class.

```php
$terminal = new Terminal();
$tableFormatter = new TableFormatter($terminal, Color::Green, Color::Blue, Color::White, 1);
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

## Interface Classes

Two components for any CLI presenting states and choices.

### Indicator

One checked item, its state, and what to do about it. Four states, each with a
shape as well as a color, so a colorless output still tells them apart:

| State | Shape | Color | Meaning |
|---|---|---|---|
| `Compliant` | ✔ | green | Conforms to what is expected |
| `Watch` | ▲ | yellow | Works, but drifts towards a failure worth preventing |
| `Failing` | ✘ | red | Does not conform, and says how to put it right |
| `Unavailable` | · | black | Cannot be checked, so says nothing about conformity |

A failing indicator without the label of its remedy is refused, so a panel
cannot announce a problem while leaving the reader without a move.

```php
use MulerTech\MTerm\Ui\Indicator;
use MulerTech\MTerm\Ui\IndicatorStatus;

Indicator::compliant('Containers running');
Indicator::watch('Disk at 82%', 'prune the unused images'); // The remedy is optional here
Indicator::failing('TLS certificate expired', 'renew it with certbot');
Indicator::unavailable('Backup age', 'host unreachable');

// When the state is only known at runtime
Indicator::of($status, 'Disk at 82%', 'prune the unused images');

Indicator::of(IndicatorStatus::Failing, 'TLS certificate expired'); // InvalidArgumentException
```

### IndicatorRenderer

Displays indicators on a common alignment, remedies in their own column.

```php
use MulerTech\MTerm\Ui\IndicatorRenderer;

(new IndicatorRenderer($terminal))->render(
    Indicator::compliant('Containers running'),
    Indicator::failing('TLS certificate expired', 'renew it with certbot'),
    Indicator::unavailable('Backup age', 'host unreachable'),
);
```

```
✔ Containers running
✘ TLS certificate expired  → renew it with certbot
· Backup age               → host unreachable
```

### Menu

A list of choices, nestable, driven with the arrow keys: `↑`/`↓` move, `ENTER`
selects, `ESC` — or `q` — goes back one level and leaves the menu at its top.

The menu carries the cycle around an action: confirm, run, report a failure,
and hand the terminal back. An item states its confirmation rather than asking
for one itself, so a destructive move cannot reach the user unconfirmed.

```php
use MulerTech\MTerm\Ui\Menu;
use MulerTech\MTerm\Ui\MenuItem;

$containers = new Menu($terminal, 'Containers');
$containers->add(MenuItem::action('Restart', $restart, 'Restart every container?'));

$menu = new Menu($terminal, 'Production');
$menu->add(MenuItem::action('Status', $status))
    ->add(MenuItem::menu('Containers', $containers));

$menu->run();
```

```
Production › Containers

❯ Restart
  Prune images

↑/↓ move   ENTER select   ESC back
```

### MenuItem

One line of a menu: either an action to run, or a menu to enter.

#### `action(string $label, callable $action, ?string $confirmation = null, ?callable $status = null, bool $awaitsKey = true): MenuItem`

An action, and the question to answer before it runs. Its report stays on
screen until a key is pressed; with `awaitsKey: false`, an action with nothing
to show hands the menu back at once.

#### `menu(string $label, Menu $submenu, ?callable $status = null): MenuItem`

A submenu, entered with `ENTER` and left with `ESC`.

#### Indicators

`$status` returns the `IndicatorStatus` the line's owner knows now, or `null`
when it knows nothing yet. It is asked for at every drawing, and drawn at the
left of the label in the colour of its state; as soon as one line of a menu
carries one, the others keep a blank column so the labels stay aligned. `null`
draws no symbol: nothing known is not something fine.

```php
$menu->add(MenuItem::menu('Serveur', $server, fn (): ?IndicatorStatus => $verdicts->of('server')))
    ->add(MenuItem::action('Tout vérifier', $checkAll, awaitsKey: false));
```

```
mtprod

❯ ✘ Serveur ›
  ✔ Parc ›
    Tout vérifier
```
