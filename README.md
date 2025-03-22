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
