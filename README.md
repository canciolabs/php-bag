# PHP Bag

[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-%5E8.5-777bb4.svg)](https://www.php.net/)

`cancio-labs/php-bag` is a small, array-backed data bag for PHP. It provides
typed accessors, dot-notation lookup for nested arrays, collection-style
operations, and JSON and date helpers behind a simple fluent API.

## Requirements

- PHP 8.5 or later

## Installation

Install the package with Composer:

```bash
composer require cancio-labs/php-bag
```

## Quick start

```php
<?php

use CancioLabs\Ds\Bag\Bag;

$bag = new Bag([
    'name' => 'Ada Lovelace',
    'contact' => [
        'email' => 'ada@example.com',
    ],
    'active' => '1',
]);

$bag->getString('name');           // 'Ada Lovelace'
$bag->getString('contact.email');  // 'ada@example.com'
$bag->getBool('active');           // true
$bag->getString('missing', 'N/A'); // 'N/A'
```

## Working with values

Create a bag from an array, replace its contents with `set()`, or add
individual values with `add()`. `merge()` appends values from arrays or other
bags. Mutating methods return the bag instance, allowing fluent calls.

```php
$bag = new Bag(['name' => 'Ada']);

$bag
    ->add('role', 'Mathematician')
    ->merge(['active' => true])
    ->set([
        'name' => 'Ada Lovelace',
        'active' => true,
    ]);

$bag->getAll();
// ['name' => 'Ada Lovelace', 'active' => true]
```

Use `getAll()`, `getKeys()`, `getValues()`, or `toArray()` to read the bag
contents. The bag also implements `Countable` and `IteratorAggregate`.

```php
count($bag);

foreach ($bag as $key => $value) {
    // ...
}
```

## Getters and defaults

Every getter accepts a key and, where applicable, a default value returned
when the key is missing or its value is `null`.

| Method | Result |
| --- | --- |
| `get()` | Value without conversion |
| `getString()` | String value |
| `getInt()` | Integer value |
| `getFloat()` | Float value |
| `getBool()` | Boolean value |
| `getArray()` | Array value |
| `getBag()` | Nested value as a new `Bag` |
| `getAlpha()` | Value containing only alphabetic characters |
| `getAlphaNum()` | Value containing only alphanumeric characters |
| `getDigits()` | Value containing only digits |
| `getDateTime()` | `DateTimeInterface` from an instance or formatted string |
| `getJson()` | Decoded JSON value |
| `getEnum()` | Case from a backed enum |

```php
use CancioLabs\Ds\Bag\Bag;

enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
}

$bag = new Bag([
    'attempts' => '3',
    'metadata' => ['source' => 'import'],
    'published_at' => '2026-09-21 12:30:00',
    'status' => 'published',
]);

$bag->getInt('attempts');                              // 3
$bag->getBag('metadata')->getString('source');         // 'import'
$bag->getDateTime('published_at');                     // DateTimeInterface
$bag->getEnum('status', Status::class);                // Status::Published
```

`getDateTime()` uses `Y-m-d H:i:s` by default; supply a format as its second
argument when needed. `getJson()` throws `JsonException` for invalid JSON,
and `getEnum()` throws `EnumNotFoundException` when the supplied class is not
an enum.

## Dot notation

Getters, `has()`, `isSet()`, and `remove()` support dot notation for nested
arrays. An exact key always takes precedence over a dotted path.

```php
$bag = new Bag([
    'user' => [
        'profile' => [
            'name' => 'Ada',
        ],
    ],
]);

$bag->has('user.profile.name');             // true
$bag->getString('user.profile.name');       // 'Ada'
$bag->remove('user.profile.name');
$bag->has('user.profile.name');             // false
```

Call `toDotNotation()` to flatten nested arrays in place:

```php
$bag = new Bag([
    'user' => [
        'name' => 'Ada',
        'address' => ['city' => 'London'],
    ],
]);

$bag->toDotNotation()->toArray();
// [
//     'user.name' => 'Ada',
//     'user.address.city' => 'London',
// ]
```

When a flattened path conflicts with a literal dotted leaf key, the literal
key takes precedence regardless of its insertion order.

## Collection operations

`filter()` and `map()` return a new bag. `every()` and `some()` return a
boolean. Each callback receives the key followed by the value.

```php
$scores = new Bag([
    'ada' => 100,
    'grace' => 95,
    'linus' => 80,
]);

$passing = $scores->filter(
    fn (string $name, int $score): bool => $score >= 90
);

$labels = $passing->map(
    fn (string $name, int $score): string => "$name: $score"
);

$scores->every(fn (string $name, int $score): bool => $score >= 80); // true
$scores->some(fn (string $name, int $score): bool => $score === 100); // true
```

## Testing

Run the test suite with:

```bash
composer tests
```

Generate an HTML coverage report with:

```bash
composer coverage
```

## License

This project is licensed under the [GNU General Public License v3.0 or
later](LICENSE).
