# Edict

Easy DI ConTainer is a PSR-11 implementation.

# Basic usage

## Static entries

You can set and get any type of value using `set(string $id, $value)` and `get($id)`.

```php
<?php
use IngeniozIT\Container\Edict;

$edict = new Edict();

$edict->set('entryId', 'entryValue');

$edict->get('entryId'); // 'entryValue'
```

You can override any entry.

```php
$edict->set('entryId', 'entryValue');
$edict->set('entryId', 'anotherEntryValue');

$edict->get('entryId'); // 'anotherEntryValue'
```

You can set multiple entries at once using `setMultiple(iterable $entries)`.

```php
$edict->setMultiple([
    'entryId' => 'entryValue',
    'anotherEntryId' => 'anotherEntryValue',
]);

$edict->get('entryId'); // 'entryValue'
$edict->get('anotherEntryId'); // 'anotherEntryValue'
```

## Dynamic entries

You can set dynamic entries using `bind(string $id, callable $callback)`.  
The callback will be called everytime you use `get($id)`.

The callback should accept one parameter of type `\Psr\Container\ContainerInterface`.

```php
$edict->bind('entryId', fn(ContainerInterface $c): string => 'foo ' . rand());

$edict->get('entryId'); // 'foo571065461'
$edict->get('entryId'); // 'foo984321175'
```

You can use the container to get values from other entries.

```php
use \Psr\Container\ContainerInterface;

$edict->bind('entryId', function (ContainerInterface $c): string {
    return $c->get('anotherEntry') . rand();
});
$edict->set('anotherEntry', 'foo');

$edict->get('entryId'); // 'foo821492074'
$edict->get('entryId'); // 'foo904232863'
```

You can bind multiple entries at once using `bindMultiple(iterable $entries)`.

```php
$edict->bindMultiple([
    'entryId' => fn(ContainerInterface $c): string => 'foo ' . rand(),
    'anotherEntryId' => fn(ContainerInterface $c): string => 'bar ' . rand(),
]);

$edict->get('entryId'); // 'foo984321175'
$edict->get('anotherEntryId'); // 'bar821492074'
```

## Autowiring

You can use Edict to instantiate classes without defining a new entry for every class.

```php
class MyClass { /* ... */ }

$edict->get(MyClass::class); // MyClass instance
```
