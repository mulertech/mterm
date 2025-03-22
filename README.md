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
use MulerTech\MTerm\Terminal;

$terminal = new Terminal();

// Display simple text
$terminal->write('Hello, World!');

// Display text with a new line
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
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

// Read input with a prompt
$name = $terminal->read('Enter your name: ');
$terminal->writeLine("Hello, $name!");

// Read input without a prompt
$terminal->writeLine('Press Enter to continue...');
$input = $terminal->read();
```

##### `readChar(string $prompt = null): string`

Reads a single character from the terminal.

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

// Read a single character with a prompt
$char = $terminal->readChar('Press any key to continue (y/n): ');
if ($char === 'y') {
    $terminal->writeLine('You pressed y!');
} else {
    $terminal->writeLine('You pressed ' . $char);
}
```

#### Output Methods

##### `write(string $text, string $color = null, bool $bold = false): void`

Writes text to the terminal without a newline.

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

// Write plain text
$terminal->write('This is regular text. ');

// Write colored text
$terminal->write('This is red text. ', 'red');

// Write bold text
$terminal->write('This is bold blue text. ', 'blue', true);

// Create a progress indicator
$terminal->write('Loading ');
for ($i = 0; $i < 5; $i++) {
    $terminal->write('.');
    sleep(1);
}
$terminal->writeLine(' Done!', 'green');
```

##### `writeLine(string $text, string $color = null, bool $bold = false): void`

Writes text to the terminal followed by a newline.

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

// Write plain text with newline
$terminal->writeLine('First line');

// Write colored text with newline
$terminal->writeLine('Success message', 'green');

// Write bold colored text with newline
$terminal->writeLine('Error message', 'red', true);

// Create a multi-line message
$terminal->writeLine('Menu Options:', 'blue', true);
$terminal->writeLine('1. Option One');
$terminal->writeLine('2. Option Two');
$terminal->writeLine('3. Exit');
```

#### Terminal Control

##### `clear(): void`

Clears the terminal screen.

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

$terminal->writeLine('This text will be cleared in 2 seconds...');
sleep(2);
$terminal->clear();
$terminal->writeLine('Screen has been cleared!', 'green');
```

##### `specialMode(): void`

Sets the terminal to special mode (non-canonical mode with echo disabled) where characters are read immediately without waiting for Enter.

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

$terminal->writeLine('Press any key to navigate (q to quit)');
$terminal->specialMode(); // Enable immediate character reading

try {
    while (true) {
        $char = $terminal->readChar();
        if ($char === 'q') {
            break;
        }
        $terminal->writeLine('You pressed: ' . $char);
    }
} finally {
    $terminal->normalMode(); // Restore normal terminal behavior
}
```

##### `normalMode(): void`

Restores the terminal to its normal mode (canonical mode with echo enabled).

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

// Example of returning to normal mode after special mode
$terminal->specialMode();
$terminal->writeLine('In special mode, press any key...');
$char = $terminal->readChar();
$terminal->normalMode();
$terminal->writeLine('Back to normal mode. You pressed: ' . $char);
```

##### `system(string $command): void`

Executes a system command.

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

// Run a system command
$terminal->writeLine('Current directory content:', 'cyan');
$terminal->system('ls -la');

// Another example
$terminal->writeLine('Date and time:', 'yellow');
$terminal->system('date');
```

#### Utility Methods

##### `supportsAnsi(): bool`

Checks if the terminal supports ANSI color codes.

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

if ($terminal->supportsAnsi()) {
    $terminal->writeLine('Your terminal supports ANSI colors!', 'green');
} else {
    $terminal->writeLine('Your terminal does not support ANSI colors.');
}
```

##### `inputStream()`

Returns the input stream resource (STDIN by default).

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

// Get the input stream and check if it's a resource
$stream = $terminal->inputStream();
if (is_resource($stream)) {
    $terminal->writeLine('Successfully accessed the input stream!', 'green');
}
```

### Available Colors

The Terminal class supports the following colors:

- black
- red
- green
- yellow
- blue
- magenta
- cyan
- white

Each color can also be displayed in bold by setting the third parameter to `true` in `write()` or `writeLine()` methods.

### Creating Interactive Menus

```php
use MulerTech\MTerm\Core\Terminal;

$terminal = new Terminal();

function showMenu($terminal) {
    $terminal->clear();
    $terminal->writeLine('=== MAIN MENU ===', 'blue', true);
    $terminal->writeLine('1. Option One');
    $terminal->writeLine('2. Option Two');
    $terminal->writeLine('3. Exit');
    $terminal->writeLine('');
    return $terminal->read('Select an option: ');
}

$running = true;
while ($running) {
    $choice = showMenu($terminal);
    
    switch ($choice) {
        case '1':
            $terminal->writeLine('You selected Option One', 'green');
            sleep(2);
            break;
        case '2':
            $terminal->writeLine('You selected Option Two', 'yellow');
            sleep(2);
            break;
        case '3':
            $terminal->writeLine('Exiting...', 'red');
            $running = false;
            break;
        default:
            $terminal->writeLine('Invalid option!', 'red');
            sleep(1);
            break;
    }
}
```

## Command System

MTerm includes a robust command system that allows you to create and manage terminal commands easily.

### CommandInterface

This interface defines the basic structure for all commands.

```php
use MulerTech\MTerm\Command\CommandInterface;
use MulerTech\MTerm\Core\Terminal;

class HelloCommand implements CommandInterface
{
    private Terminal $terminal;
    
    public function __construct(Terminal $terminal) 
    {
        $this->terminal = $terminal;
    }
    
    public function getName(): string
    {
        return 'hello';
    }
    
    public function getDescription(): string
    {
        return 'Displays a greeting to the specified name';
    }
    
    public function execute(array $args = []): int
    {
        $name = $args[0] ?? 'World';
        $this->terminal->writeLine("Hello, $name!", 'green');
        return 0; // Success
    }
}
```

### AbstractCommand

A base class that implements basic functionality for CommandInterface.

```php
use MulerTech\MTerm\Command\AbstractCommand;
use MulerTech\MTerm\Core\Terminal;

class DateCommand extends AbstractCommand
{
    public function __construct(Terminal $terminal) 
    {
        parent::__construct($terminal);
        $this->name = 'date';
        $this->description = 'Displays the current date and time';
    }
    
    public function execute(array $args = []): int
    {
        $format = $args[0] ?? 'Y-m-d H:i:s';
        $this->terminal->writeLine(date($format), 'cyan');
        
        // Show command help when --help argument is provided
        if (in_array('--help', $args, true)) {
            $this->showHelp();
        }
        
        return 0;
    }
}

// Example usage:
$terminal = new Terminal();
$dateCommand = new DateCommand($terminal);

// Get command information
$name = $dateCommand->getName(); // Returns 'date'
$description = $dateCommand->getDescription(); // Returns 'Displays the current date and time'

// Show help information
$dateCommand->showHelp(); // Displays: "date: Displays the current date and time"

// Execute the command
$dateCommand->execute(); // Displays current date in default format
$dateCommand->execute(['d/m/Y']); // Displays current date in specified format
```

### CommandRegistry

Manages a collection of commands, allowing you to register, retrieve, and execute them.

```php
use MulerTech\MTerm\Command\CommandRegistry;
use MulerTech\MTerm\Core\Terminal;

// Create a terminal and command registry
$terminal = new Terminal();
$registry = new CommandRegistry();

// Create some commands
$helloCommand = new HelloCommand($terminal);
$dateCommand = new DateCommand($terminal);

// Register commands
$registry->register($helloCommand);
$registry->register($dateCommand);

// Check if a command exists
if ($registry->has('hello')) {
    $terminal->writeLine('Hello command is registered!', 'green');
}

// Get a specific command
$command = $registry->get('date');
if ($command !== null) {
    $terminal->writeLine('Found command: ' . $command->getName());
    $command->execute();
}

// Get all registered commands
$allCommands = $registry->getAll();
$terminal->writeLine('Registered commands:', 'yellow');
foreach ($allCommands as $name => $cmd) {
    $terminal->writeLine(" - $name: " . $cmd->getDescription());
}

// Execute a command
try {
    $exitCode = $registry->execute('hello', ['User']);
    $terminal->writeLine("Command executed with exit code: $exitCode");
    
    // This will throw an exception
    $registry->execute('unknown-command');
} catch (\InvalidArgumentException $e) {
    $terminal->writeLine($e->getMessage(), 'red');
}
```

### Building a Simple CLI Application

Here's an example of how to build a simple CLI application using MTerm's command system:

```php
use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Command\CommandRegistry;

// Initialize terminal and command registry
$terminal = new Terminal();
$registry = new CommandRegistry();

// Register available commands
$registry->register(new HelloCommand($terminal));
$registry->register(new DateCommand($terminal));
// Register more commands...

// Display welcome message
$terminal->writeLine('Welcome to My CLI Application', 'blue', true);
$terminal->writeLine('Type "help" to see available commands or "exit" to quit', 'yellow');

// Main application loop
while (true) {
    // Show prompt and get command
    $input = $terminal->read('> ');
    $parts = explode(' ', $input);
    $commandName = array_shift($parts);
    
    // Handle exit command
    if ($commandName === 'exit') {
        $terminal->writeLine('Goodbye!', 'cyan');
        break;
    }
    
    // Handle help command
    if ($commandName === 'help') {
        $terminal->writeLine('Available commands:', 'green', true);
        foreach ($registry->getAll() as $name => $command) {
            $terminal->writeLine(" - $name: " . $command->getDescription());
        }
        continue;
    }
    
    // Execute the command if it exists
    try {
        if ($registry->has($commandName)) {
            $exitCode = $registry->execute($commandName, $parts);
            if ($exitCode !== 0) {
                $terminal->writeLine("Command returned non-zero exit code: $exitCode", 'yellow');
            }
        } else {
            $terminal->writeLine("Unknown command: $commandName", 'red');
            $terminal->writeLine('Type "help" to see available commands', 'yellow');
        }
    } catch (\Exception $e) {
        $terminal->writeLine('Error: ' . $e->getMessage(), 'red', true);
    }
}
```

## Application Class

The Application class implements a singleton pattern for managing terminal interactions and command execution.

### Method Reference

#### `getInstance(): Application`

Gets the singleton instance of the Application class.

```php
use MulerTech\MTerm\Core\Application;

// Get the application instance
$app = Application::getInstance();

// The same instance will be returned on subsequent calls
$sameApp = Application::getInstance();
```

#### `getTerminal(): Terminal`

Returns the Terminal instance managed by the Application.

```php
use MulerTech\MTerm\Core\Application;

$app = Application::getInstance();
$terminal = $app->getTerminal();

// Now you can use the terminal to interact with the user
$terminal->writeLine('Welcome to MTerm Application!', 'green');
$name = $terminal->read('Please enter your name: ');
$terminal->writeLine("Hello, $name!", 'blue');
```

#### `getCommandRunner(): CommandRunner`

Returns the CommandRunner instance managed by the Application.

```php
use MulerTech\MTerm\Core\Application;

$app = Application::getInstance();
$commandRunner = $app->getCommandRunner();

// Now you can execute system commands
$result = $commandRunner->run('ls -la');
$app->getTerminal()->writeLine('Command output:', 'yellow');
foreach ($result['output'] as $line) {
    $app->getTerminal()->writeLine($line);
}
```

#### `run(): void`

Starts the application's main execution loop.

```php
use MulerTech\MTerm\Core\Application;

// Get the application instance and run it
$app = Application::getInstance();
$app->run();
```

## CommandRunner Class

The CommandRunner class provides methods to execute system commands and capture their output.

### Method Reference

#### `run(string $command): array`

Executes a command and returns an array containing the output and return code.

```php
use MulerTech\MTerm\Core\CommandRunner;
use MulerTech\MTerm\Core\Terminal;

$commandRunner = new CommandRunner();
$terminal = new Terminal();

// Run a command and display its output
$result = $commandRunner->run('echo "Hello from the command line"');

$terminal->writeLine('Command output:', 'cyan');
foreach ($result['output'] as $line) {
    $terminal->writeLine($line);
}

$terminal->writeLine('Return code: ' . $result['returnCode'], 'yellow');

// Example with a more complex command
$result = $commandRunner->run('find /path/to/directory -name "*.php" | wc -l');
$terminal->writeLine('Number of PHP files: ' . $result['output'][0], 'green');
```

#### `runWithStderr(string $command): array`

Executes a command and returns an array containing stdout, stderr, and the return code.

```php
use MulerTech\MTerm\Core\CommandRunner;
use MulerTech\MTerm\Core\Terminal;

$commandRunner = new CommandRunner();
$terminal = new Terminal();

// Run a command that might produce errors
$result = $commandRunner->runWithStderr('ls /nonexistent/directory');

// Display standard output if any
if ($result['stdout']) {
    $terminal->writeLine('Command output:', 'green');
    $terminal->writeLine($result['stdout']);
}

// Display error output if any
if ($result['stderr']) {
    $terminal->writeLine('Error output:', 'red');
    $terminal->writeLine($result['stderr']);
}

$terminal->writeLine('Return code: ' . $result['returnCode'], 'yellow');

// Example with a successful command
$result = $commandRunner->runWithStderr('echo "Success" && echo "Warning" >&2');
$terminal->writeLine('Standard output: ' . $result['stdout'], 'green');
$terminal->writeLine('Error output: ' . $result['stderr'], 'yellow');
```

### Combining Application, Terminal, and CommandRunner

Here's an example of how to use all three classes together to build a simple command execution utility:

```php
use MulerTech\MTerm\Core\Application;

// Get the application instance
$app = Application::getInstance();
$terminal = $app->getTerminal();
$commandRunner = $app->getCommandRunner();

// Welcome message
$terminal->clear();
$terminal->writeLine('=== Command Execution Utility ===', 'blue', true);
$terminal->writeLine('Type a command to execute or "exit" to quit', 'yellow');
$terminal->writeLine('');

// Main loop
while (true) {
    $command = $terminal->read('> ');
    
    if ($command === 'exit') {
        $terminal->writeLine('Goodbye!', 'green');
        break;
    }
    
    if (empty($command)) {
        continue;
    }
    
    $terminal->writeLine('Executing: ' . $command, 'cyan');
    
    $result = $commandRunner->runWithStderr($command);
    
    if ($result['stdout']) {
        $terminal->writeLine('Output:', 'green');
        $terminal->writeLine($result['stdout']);
    }
    
    if ($result['stderr']) {
        $terminal->writeLine('Errors:', 'red');
        $terminal->writeLine($result['stderr']);
    }
    
    $status = $result['returnCode'] === 0 ? 'Success' : 'Failed';
    $color = $result['returnCode'] === 0 ? 'green' : 'red';
    $terminal->writeLine('Status: ' . $status . ' (code: ' . $result['returnCode'] . ')', $color);
    $terminal->writeLine('');
}
```

## Form Classes

### AbstractField

#### `__construct(string $name, string $label)`

Constructor for the AbstractField class.

- **Parameters:**
  - `string $name`: The name of the field.
  - `string $label`: The label of the field.

#### `getName(): string`

Returns the name of the field.

#### `getLabel(): string`

Returns the label of the field.

#### `getDescription(): ?string`

Returns the description of the field.

#### `setDescription(string $description): self`

Sets the description of the field.

- **Parameters:**
  - `string $description`: The description of the field.

#### `isRequired(): bool`

Checks if the field is required.

#### `setRequired(bool $required = true): self`

Sets whether the field is required.

- **Parameters:**
  - `bool $required`: Whether the field is required.

#### `getDefault(): string|int|float|array|null`

Returns the default value of the field.

#### `setDefault(string|int|float|array $defaultValue): self`

Sets the default value of the field.

- **Parameters:**
  - `string|int|float|array $defaultValue`: The default value of the field.

#### `clearErrors(): void`

Clears the errors of the field.

#### `addValidator(ValidatorInterface $validator): self`

Adds a validator to the field.

- **Parameters:**
  - `ValidatorInterface $validator`: The validator to add.

#### `validate(string|int|float|array|null $value): array`

Validates the field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

#### `processInput(string $input): string|int|float|array`

Processes the user input.

- **Parameters:**
  - `string $input`: The raw input from the user.

#### `setTerminal(Terminal $terminal): self`

Sets the terminal instance for the field.

- **Parameters:**
  - `Terminal $terminal`: The terminal instance.

### CheckboxField

#### `setCheckedValue(string $value): self`

Sets the checked value for the checkbox.

- **Parameters:**
  - `string $value`: The checked value.

#### `setUncheckedValue(string $value): self`

Sets the unchecked value for the checkbox.

- **Parameters:**
  - `string $value`: The unchecked value.

#### `processInput(string $input): string|int|float|array`

Processes the user input for the checkbox.

- **Parameters:**
  - `string $input`: The raw input from the user.

### ColorField

#### `validate(string|int|float|array|null $value): array`

Validates the color field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

### DateField

#### `setFormat(string $format): self`

Sets the date format.

- **Parameters:**
  - `string $format`: The date format.

#### `getFormat(): string`

Returns the date format.

#### `processInput(string $input): string|int|float|array`

Processes the user input for the date field.

- **Parameters:**
  - `string $input`: The raw input from the user.

#### `validate(string|int|float|array|null $value): array`

Validates the date field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

### EmailField

#### `validate(string|int|float|array|null $value): array`

Validates the email field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

### FieldInterface

#### `getName(): string`

Returns the name of the field.

#### `getLabel(): string`

Returns the label of the field.

#### `getDescription(): ?string`

Returns the description of the field.

#### `setDescription(string $description): self`

Sets the description of the field.

- **Parameters:**
  - `string $description`: The description of the field.

#### `isRequired(): bool`

Checks if the field is required.

#### `setRequired(bool $required = true): self`

Sets whether the field is required.

- **Parameters:**
  - `bool $required`: Whether the field is required.

#### `clearErrors(): void`

Clears the errors of the field.

#### `setDefault(string|int|float|array $defaultValue): self`

Sets the default value of the field.

- **Parameters:**
  - `string|int|float|array $defaultValue`: The default value of the field.

#### `getDefault(): string|int|float|array|null`

Returns the default value of the field.

#### `processInput(string $input): string|int|float|array`

Processes the user input.

- **Parameters:**
  - `string $input`: The raw input from the user.

#### `validate(string|int|float|array|null $value): array`

Validates the field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

#### `setTerminal(Terminal $terminal): self`

Sets the terminal instance for the field.

- **Parameters:**
  - `Terminal $terminal`: The terminal instance.

### FileField

#### `setAllowedExtensions(array $extensions): self`

Sets the allowed file extensions.

- **Parameters:**
  - `array $extensions`: The allowed file extensions.

#### `setMaxSize(int $bytes): self`

Sets the maximum file size.

- **Parameters:**
  - `int $bytes`: The maximum file size in bytes.

#### `processInput(string $input): string|int|float|array`

Processes the user input for the file field.

- **Parameters:**
  - `string $input`: The raw input from the user.

#### `validate(string|int|float|array|null $value): array`

Validates the file field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

### NumberField

#### `setMin(?float $min): self`

Sets the minimum value for the number field.

- **Parameters:**
  - `?float $min`: The minimum value.

#### `setMax(?float $max): self`

Sets the maximum value for the number field.

- **Parameters:**
  - `?float $max`: The maximum value.

#### `setAllowFloat(bool $allowFloat): self`

Sets whether to allow floating-point numbers.

- **Parameters:**
  - `bool $allowFloat`: Whether to allow floating-point numbers.

#### `processInput(string $input): string|int|float|array`

Processes the user input for the number field.

- **Parameters:**
  - `string $input`: The raw input from the user.

#### `validate(string|int|float|array|null $value): array`

Validates the number field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

### PasswordField

#### `isMaskInput(): bool`

Checks if the input should be masked.

#### `setMaskInput(bool $maskInput = true): self`

Sets whether the input should be masked.

- **Parameters:**
  - `bool $maskInput`: Whether the input should be masked.

#### `getMaskChar(): string`

Returns the mask character.

#### `setMaskChar(string $maskChar): self`

Sets the mask character.

- **Parameters:**
  - `string $maskChar`: The mask character.

#### `parseInput(string $input): string`

Parses the user input.

- **Parameters:**
  - `string $input`: The raw input from the user.

#### `processInput(string $input = ''): string`

Processes the user input for the password field.

- **Parameters:**
  - `string $input`: The raw input from the user.

### RadioField

#### `__construct(string $name, string $label)`

Constructor for the RadioField class.

- **Parameters:**
  - `string $name`: The name of the field.
  - `string $label`: The label of the field.

#### `setMultipleSelection(bool $multipleSelection = true): self`

Sets whether multiple selection is allowed.

- **Parameters:**
  - `bool $multipleSelection`: Whether multiple selection is allowed.

### RangeField

#### `__construct(string $name, string $label)`

Constructor for the RangeField class.

- **Parameters:**
  - `string $name`: The name of the field.
  - `string $label`: The label of the field.

#### `setStep(int $step): self`

Sets the step value for the range field.

- **Parameters:**
  - `int $step`: The step value.

#### `getStep(): int`

Returns the step value for the range field.

#### `validate(string|int|float|array|null $value): array`

Validates the range field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

### SelectField

#### `__construct(string $name, string $label, bool $multipleSelection = false)`

Constructor for the SelectField class.

- **Parameters:**
  - `string $name`: The name of the field.
  - `string $label`: The label of the field.
  - `bool $multipleSelection`: Whether multiple selection is allowed.

#### `setOptions(array $options): self`

Sets the options for the select field.

- **Parameters:**
  - `array $options`: The options for the select field.

#### `setMultipleSelection(bool $multipleSelection = true): self`

Sets whether multiple selection is allowed.

- **Parameters:**
  - `bool $multipleSelection`: Whether multiple selection is allowed.

#### `isMultipleSelection(): bool`

Checks if multiple selection is allowed.

#### `parseInput(string $input): string`

Parses the user input.

- **Parameters:**
  - `string $input`: The raw input from the user.

#### `processInput(string $input = ''): string|array`

Processes the user input for the select field.

- **Parameters:**
  - `string $input`: The raw input from the user.

#### `renderSelectMultipleField(Terminal $terminal): array`

Renders the select field for multiple selection.

- **Parameters:**
  - `Terminal $terminal`: The terminal instance.

#### `renderSelectSingleField(Terminal $terminal): string`

Renders the select field for single selection.

- **Parameters:**
  - `Terminal $terminal`: The terminal instance.

#### `validate(string|int|float|array|null $value): array`

Validates the select field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

#### `getCurrentOption(): string`

Returns the current selected option.

### TextField

#### `setMinLength(int $minLength): self`

Sets the minimum length for the text field.

- **Parameters:**
  - `int $minLength`: The minimum length.

#### `setMaxLength(?int $maxLength): self`

Sets the maximum length for the text field.

- **Parameters:**
  - `?int $maxLength`: The maximum length.

#### `validate(string|int|float|array|null $value): array`

Validates the text field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

### UrlField

#### `validate(string|int|float|array|null $value): array`

Validates the URL field value.

- **Parameters:**
  - `string|int|float|array|null $value`: The value to validate.

### Form

#### `__construct(Terminal $terminal)`

Constructor for the Form class.

- **Parameters:**
  - `Terminal $terminal`: The terminal instance.

#### `addField(FieldInterface $field): self`

Adds a field to the form.

- **Parameters:**
  - `FieldInterface $field`: The field to add.

#### `handle(): void`

Handles the form submission.

#### `isSubmitted(): bool`

Checks if the form has been submitted.

#### `isValid(): bool`

Checks if the form is valid.

#### `getValues(): array`

Returns all form values.

#### `getValue(string $fieldName): string|int|float|array|null`

Returns a specific form value.

- **Parameters:**
  - `string $fieldName`: The name of the field.

### FormRenderer

#### `__construct(Terminal $terminal)`

Constructor for the FormRenderer class.

- **Parameters:**
  - `Terminal $terminal`: The terminal instance.

#### `renderField(FieldInterface $field): string|array`

Renders a field.

- **Parameters:**
  - `FieldInterface $field`: The field to render.

#### `renderErrors(array $errors): void`

Renders the errors.

- **Parameters:**
  - `array $errors`: The errors to render.

#### `clear(): void`

Clears the terminal screen.

### ValidatorInterface

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

### AbstractValidator

#### `__construct(string $errorMessage)`

Constructor for the AbstractValidator class.

- **Parameters:**
  - `string $errorMessage`: The error message.

#### `getErrorMessage(): string`

Returns the error message.

### ChoiceValidator

#### `__construct(array $choices, bool $strict = true, string $errorMessage = "Selected value is not a valid choice.")`

Constructor for the ChoiceValidator class.

- **Parameters:**
  - `array $choices`: The valid choices.
  - `bool $strict`: Whether to use strict comparison.
  - `string $errorMessage`: The error message.

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

### DateValidator

#### `__construct(string $format = 'Y-m-d', ?DateTimeInterface $minDate = null, ?DateTimeInterface $maxDate = null, ?string $errorMessage = null)`

Constructor for the DateValidator class.

- **Parameters:**
  - `string $format`: The date format.
  - `?DateTimeInterface $minDate`: The minimum date.
  - `?DateTimeInterface $maxDate`: The maximum date.
  - `?string $errorMessage`: The error message.

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

### EmailValidator

#### `__construct(string $errorMessage = "Please enter a valid email address.")`

Constructor for the EmailValidator class.

- **Parameters:**
  - `string $errorMessage`: The error message.

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

### IpAddressValidator

#### `__construct(bool $allowIPv4 = true, bool $allowIPv6 = true, bool $allowPrivate = true, bool $allowReserved = true, string $errorMessage = "Please enter a valid IP address.")`

Constructor for the IpAddressValidator class.

- **Parameters:**
  - `bool $allowIPv4`: Whether to allow IPv4 addresses.
  - `bool $allowIPv6`: Whether to allow IPv6 addresses.
  - `bool $allowPrivate`: Whether to allow private addresses.
  - `bool $allowReserved`: Whether to allow reserved addresses.
  - `string $errorMessage`: The error message.

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

### LengthValidator

#### `__construct(?int $min = null, ?int $max = null, ?string $errorMessage = null)`

Constructor for the LengthValidator class.

- **Parameters:**
  - `?int $min`: The minimum length.
  - `?int $max`: The maximum length.
  - `?string $errorMessage`: The error message.

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

### NotEmptyValidator

#### `__construct(string $errorMessage = "This value cannot be empty.")`

Constructor for the NotEmptyValidator class.

- **Parameters:**
  - `string $errorMessage`: The error message.

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

### NumericRangeValidator

#### `__construct(?float $min = null, ?float $max = null, ?string $errorMessage = null)`

Constructor for the NumericRangeValidator class.

- **Parameters:**
  - `?float $min`: The minimum value.
  - `?float $max`: The maximum value.
  - `?string $errorMessage`: The error message.

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

### PatternValidator

#### `__construct(string $pattern, string $errorMessage = "Value does not match required pattern.")`

Constructor for the PatternValidator class.

- **Parameters:**
  - `string $pattern`: The pattern to match.
  - `string $errorMessage`: The error message.

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

### RegexValidator

#### `__construct(string $pattern, string $errorMessage = "This value is not valid.")`

Constructor for the RegexValidator class.

- **Parameters:**
  - `string $pattern`: The pattern to match.
  - `string $errorMessage`: The error message.

#### `validate(mixed $value): ?string`

Validates a value.

- **Parameters:**
  - `mixed $value`: The value to validate.

## Utils Classes

### ProgressBar

#### `__construct(Terminal $terminal, int $total = 100, int $width = 50, string $completeChar = '=', string $incompleteChar = '-', string $color = Terminal::COLORS['green'])`

Constructor for the ProgressBar class.

- **Parameters:**
  - `Terminal $terminal`: The terminal instance.
  - `int $total`: The total value.
  - `int $width`: The width of the progress bar.
  - `string $completeChar`: The character for completed progress.
  - `string $incompleteChar`: The character for incomplete progress.
  - `string $color`: The color of the progress bar.

#### `start(): void`

Starts the progress bar.

#### `advance(int $step = 1): void`

Advances the progress bar by a specific amount.

- **Parameters:**
  - `int $step`: The amount to advance.

#### `setProgress(int $current): void`

Sets the progress to a specific value.

- **Parameters:**
  - `int $current`: The new progress value.

#### `finish(): void`

Finishes the progress bar.

### TableFormatter

#### `__construct(Terminal $terminal, string $headerColor = Terminal::COLORS['green'], string $borderColor = Terminal::COLORS['blue'], string $cellColor = Terminal::COLORS['white'], int $padding = 1)`

Constructor for the TableFormatter class.

- **Parameters:**
  - `Terminal $terminal`: The terminal instance.
  - `string $headerColor`: The color of the header.
  - `string $borderColor`: The color of the border.
  - `string $cellColor`: The color of the cells.
  - `int $padding`: The padding for the cells.

#### `renderTable(array $headers, array $rows): void`

Formats and renders a table.

- **Parameters:**
  - `array $headers`: The table headers.
  - `array $rows`: The table data rows.
