# Console Awesome Table

Pretty, width-aware tables for Laravel console commands.

This package provides a small `HasAwesomeTable` trait that adds helper methods for rendering data in a readable way in the terminal. It is designed to be mixed into your `Illuminate\Console\Command` classes.

## Installation

Require the package via Composer:

```bash
composer require oneawebmarketing/console-awesome-table
```

Laravel will auto-discover the package via Composer's PSR-4 autoloading.

## Basic usage

### Add the trait to a command

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use AwesomeTable\HasAwesomeTable;

class ShowUsers extends Command
{
    use HasAwesomeTable;

    protected $signature = 'demo:users';
    protected $description = 'Show a list of users in an awesome table';

    public function handle(): int
    {
        $this->title('Users');

        $rows = [
            ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
            ['id' => 2, 'name' => 'Bob',   'email' => 'bob@example.com'],
        ];

        // Headings provided explicitly
        $this->awesomeTable(
            ['id', 'name', 'email'],
            $rows,
        );

        return self::SUCCESS;
    }
}
```

Run the command:

```bash
php artisan demo:users
```

You will see a framed title followed by a table or a block-style layout, depending on your terminal width.

## API

The `HasAwesomeTable` trait (defined in `src/AwesomeTable.php`) adds the following methods to your command:

- **`title(string $title): void`**  
  Renders a framed title banner in the console.

- **`awesomeTable(array $headings = [], array $rows = [], bool $showUnusedKeys = true): void`**  
  Renders tabular data using the underlying `table()` helper on the command when it fits in the terminal width; otherwise, it falls back to a compact block layout.

  - **`$headings`**: A list of column names. If empty, the keys from the first row are used.
  - **`$rows`**: An array of associative arrays, one per row.
  - **`$showUnusedKeys`**: When `true`, any keys present in the first row but not displayed as headings are listed as "Undisplayed fields" after the output.

### Automatic headings

If you do not pass headings, the first row's keys are used:

```php
$rows = [
    ['name' => 'Alice', 'email' => 'alice@example.com'],
    ['name' => 'Bob',   'email' => 'bob@example.com'],
];

// Headings: ['name', 'email']
$this->awesomeTable([], $rows);
```

### Handling extra fields

If your rows contain more keys than you list in `$headings`, the undisplayed keys are collected and printed as an info line (when `$showUnusedKeys` is `true`):

```php
$rows = [
    ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com', 'role' => 'admin'],
];

// Only "id", "name" and "email" shown in the table
$this->awesomeTable(['id', 'name', 'email'], $rows);

// Console (simplified):
// Undisplayed fields: role
```

## Output mode & terminal width

The trait estimates the width of the table (based on the headings and first row) and compares it to the current terminal width. Depending on the result:

- **Fits in width**: Data is sent to the command's `table()` method (standard Symfony/Laravel-style table).
- **Too wide**: Data is printed as framed "blocks", one block per row, with aligned keys and values so that long rows remain readable on narrow terminals.

This makes it convenient to display large or verbose data structures without worrying about wrapping or truncated columns.

## License

This package is open-sourced software licensed under the **MIT license**.

