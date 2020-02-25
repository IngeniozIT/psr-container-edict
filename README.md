# Edict

Easy DI ConTainer is a PSR-11 implementation.

# Basic usage

## Static entries

You can set and get any type of value using `set(string $id, $value)` and `get($id)`.

```php
<?php
use IngeniozIT\Container\Edict;

$container = new Edict();

$container->set('entryId', 'entryValue');

$container->get('entryId'); // 'entryValue'
```

You can set multiple entries at once using `setMultiple(iterable $entries)`.

```php
$container->setMultiple([
    'entryId' => 'entryValue',
    'anotherEntryId' => 'anotherEntryValue',
]);

$container->get('entryId'); // 'entryValue'
$container->get('anotherEntryId'); // 'anotherEntryValue'
```
