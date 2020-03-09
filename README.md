# Edict

Easy DI ConTainer is a slim, [PSR 11](https://www.php-fig.org/psr/psr-11/), framework-agnostic dependency injection container.



## Table of Contents

* [Informations](#informations)
* [Installation](#installation)
* [Documentation](#documentation)
    1. [Basic entries](#basic-entries)
    2. [Dynamic entries](#dynamic-entries)
    3. [Autowiring](#autowiring)
    4. [Aliases](#aliases)

## Informations

| Info | Value |
|-|-|
| Current version | [![Packagist Version](https://img.shields.io/packagist/v/ingenioz-it/http-message.svg)](https://packagist.org/packages/ingenioz-it/http-message) |
| Requires | ![PHP from Packagist](https://img.shields.io/packagist/php-v/ingenioz-it/http-message.svg) |
| Tests | [![Build Status](https://travis-ci.com/IngeniozIT/psr-http-message.svg?branch=master)](https://travis-ci.com/IngeniozIT/psr-http-message) |
| Code coverage | [![Code Coverage](https://codecov.io/gh/IngeniozIT/psr-http-message/branch/master/graph/badge.svg)](https://codecov.io/gh/IngeniozIT/psr-http-message) |
| License | ![Packagist](https://img.shields.io/packagist/l/ingenioz-it/http-message.svg) |

## Installation

Via composer

```sh
composer require ingenioz-it/edict
```

Via git

```sh
git clone https://github.com/IngeniozIT/psr-container-edict.git
```

## Documentation

### Basic entries

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

### Dynamic entries

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

### Autowiring

You can use Edict to instantiate classes without defining a new entry for every class.

```php
class MyClass { /* ... */ }

$edict->get(MyClass::class); // MyClass instance
```

If the class constructor needs parameters, Edict will automatically resolve the dependencies.

```php
class MyOtherClass
{
    public function __construct(MyClass $myclass) { /* ... */ }
}

$edict->get(MyOtherClass::class); // MyOtherClass instance
```

### Aliases

Some classes constructors accept interfaces as parameters.  
In this case, during autowiring, Edict is not able to tell which implementation of this interface you want to use.

You can use `alias(string $sourceId, string $destId)` to bind an interface to one of its implementations.

```php
interface MyInterface { /* ... */ }
class MyClass implements MyInterface { /* ... */ }

$edict->bind('foo', function (ContainerInterface $container): MyClass {
    return new MyClass();
}
$edict->alias(MyInterface::class, 'foo');

$edict->get(MyInterface::class); // MyClass instance
```

You can also use Edict's autowiring by giving a class name instead of an entry id.

```php
interface MyInterface { /* ... */ }
class MyClass implements MyInterface { /* ... */ }

$edict->alias(MyInterface::class, MyClass::class);

$edict->get(MyInterface::class); // MyClass instance
```

You can bind multiple interfaces at once using `aliases(iterable $entries)`.

```php
$edict->aliases([
    MyInterface::class => MyClass::class,
    MyOtherInterface::class => MyOtherClass::class,
]);

$edict->get(MyInterface::class); // MyClass instance
$edict->get(MyOtherInterface::class); // MyOtherClass instance
```

Aliases can also be used to manage your code plugins (databases mapping etc).

```php

$edict->bindMultiple([
    'database1' => function (ContainerInterface $c) { /* ... */ },
    'database2' => function (ContainerInterface $c) { /* ... */ },
    'database3' => function (ContainerInterface $c) { /* ... */ },
]);

$edict->aliases([
    'usersDatabase' => 'database1',
    'monitoringDatabase' => 'database2',
    'transactionsDatabase' => 'database2',
    'topSecretDatabase' => 'database3',
]);
```
