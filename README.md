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
            ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com', 'gender' => 'FEMALE', 'age' => 42],
            ['id' => 2, 'name' => 'Bob',   'email' => 'bob@example.com', 'gender' => 'MALE', 'age' => 20],
        ];

        // Headings provided explicitly, left over heading keys gets shown as info
        $this->awesomeTable(
            ['id', 'name', 'email'],
            $rows,
        );

        return self::SUCCESS;
    }
}
```

Outputs:
```bash
#########################
#                       #
#         Users         #
#                       #
#########################

+----+-------+-------------------+
| id | name  | email             |
+----+-------+-------------------+
| 1  | Alice | alice@example.com |
| 2  | Bob   | bob@example.com   |
+----+-------+-------------------+
Undisplayed fields: gender, age
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

  - **`$headings`**: A list of column names. If empty, the keys from the first row are used. Headings support two directives:
    - **Dot notation** (`address.city`) — access nested array/object values via Laravel's `data_get()`.
    - **`implode:` prefix** (`implode:tags`) — joins array values with `", "`, filtering out empty entries. The column header displays without the prefix.
  - **`$rows`**: An array of associative arrays, one per row.
  - **`$showUnusedKeys`**: When `true`, any keys present in the first row but not displayed as headings are listed as "Undisplayed fields" after the output.

### Dot notation

Headings support Laravel's dot notation to access nested array or object values:

```php
$rows = [
    ['id' => 1, 'name' => 'Alice', 'address' => ['city' => 'Berlin', 'zip' => '10115']],
    ['id' => 2, 'name' => 'Bob',   'address' => ['city' => 'Hamburg', 'zip' => '20095']],
];

$this->awesomeTable(['id', 'name', 'address.city'], $rows);
```

```
+----+-------+--------------+
| id | name  | address.city |
+----+-------+--------------+
| 1  | Alice | Berlin       |
| 2  | Bob   | Hamburg      |
+----+-------+--------------+
```

### Imploding array values

Prefix a heading with `implode:` to join array values with `", "` instead of showing `+ Array`. Empty entries are filtered out before joining.

```php
$rows = [
    ['name' => 'Alice', 'tags' => ['admin', 'editor', '']],
    ['name' => 'Bob',   'tags' => ['viewer']],
];

$this->awesomeTable(['name', 'implode:tags'], $rows);
```

```
+-------+---------------+
| name  | tags          |
+-------+---------------+
| Alice | admin, editor |
| Bob   | viewer        |
+-------+---------------+
```

The column header displays as `tags` (without the `implode:` prefix). Dot notation and `implode:` can be combined: `implode:meta.tags`.

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

