# Edict

EDICT (**E**asy **DI** **C**on**T**ainer) is a slim, [PSR 11](https://www.php-fig.org/psr/psr-11/), framework-agnostic dependency injection container.

Cool features:

- [Autowiring](#autowiring) support *(can be toggled off)*
- [PHP attributes](#php-attributes) support
- 100% tested and documented
- Uses an up-to-date PHP version

## About

| Info | Value |
|---|---|
| Latest release | [![Packagist Version](https://img.shields.io/packagist/v/ingenioz-it/edict)](https://packagist.org/packages/ingenioz-it/edict) |
| Requires | ![PHP from Packagist](https://img.shields.io/packagist/php-v/ingenioz-it/edict.svg) |
| License | ![Packagist](https://img.shields.io/packagist/l/ingenioz-it/edict) |
| Unit tests | [![tests](https://github.com/IngeniozIT/psr-container-edict/actions/workflows/1-tests.yml/badge.svg)](https://github.com/IngeniozIT/psr-container-edict/actions/workflows/1-tests.yml) |
| Code coverage | [![Code Coverage](https://codecov.io/gh/IngeniozIT/psr-container-edict/branch/master/graph/badge.svg)](https://codecov.io/gh/IngeniozIT/psr-container-edict) |
| Code quality | [![code-quality](https://github.com/IngeniozIT/psr-container-edict/actions/workflows/2-code-quality.yml/badge.svg)](https://github.com/IngeniozIT/psr-container-edict/actions/workflows/2-code-quality.yml) |
| Quality tested with | [phpunit](https://github.com/sebastianbergmann/phpunit), [phan](https://github.com/phan/phan), [psalm](https://github.com/vimeo/psalm), [phpcs](https://github.com/squizlabs/PHP_CodeSniffer), [phpstan](https://github.com/phpstan/phpstan), [infection](https://github.com/infection/infection) |

## Installation

```sh
composer require ingenioz-it/edict
```

## How to use

To use Edict, put the following line into your code:

```php
$edict = new \IngeniozIT\Edict\Container();
```

Edict is now ready to go. Yes, it's that simple !

### Types of entries

Depending on your needs, you can create several types of entries:

#### Simple values

Scalar, arrays, objects ... pretty much whatever you want.

```php
use function IngeniozIT\Edict\value;

$edict->set('entryName', value('entryValue'));

$edict->get('entryName'); // 'entryValue'
```

#### Aliases

Maps an entry to another entry.

```php
use function IngeniozIT\Edict\alias;

$edict->set('entryName', value(42));
$edict->set('aliasEntryName', alias('entryName'));

$edict->get('aliasEntryName'); // 42
```

#### Dynamic values

These entries will be resolved everytime you call them.

```php
use function IngeniozIT\Edict\dynamic;

$edict->set('entryName', dynamic(fn() => date('H:i:s')));

$edict->get('entryName'); // '08:36:51'
sleep(1);
$edict->get('entryName'); // '08:36:52' <-- one second has passed
```

#### Lazy loaded values

These entries will be resolved only once. Use them when you want to use the same object across your application (like a database handle).

```php
use function IngeniozIT\Edict\lazyload;

$edict->set('entryName', lazyload(fn() => new PDO(/* ... */)));

$edict->get('entryName'); // Your PDO instance
$edict->get('entryName'); // The same PDO instance
```

#### Plain entry

These entries will return the result of the callback you give them. Use them for advanced injections.

```php
use function IngeniozIT\Edict\entry;

$edict->set('entryName', entry(fn() => 42));

$edict->get('entryName'); // 42
```

### Setting multiple entries at once

You can set multiple entries at once:

```php
$edict->setMany([
    'entry1' => value('foo'),
    'entry2' => value('bar'),
    'entry3' => alias('someEntry'),
]);
```

### Using the container inside an entry

You can use the container inside the callback to fetch other entries.

```php
use Psr\Container\ContainerInterface;

// Put this inside a local config file.
$edict->setMany([
    'db.dsn' => value('mysql:dbname=mydb;host=localhost'),
    'db.username' => value('root'),
    'db.password' => value('secret'),
);

// To instantiate the db
$edict->set('database', lazyload(
    fn(ContainerInterface $container) => new PDO(
        $container->get('db.dsn'),
        $container->get('db.username'),
        $container->get('db.password')
    ))
);

$edict->get('database'); // Your PDO object
```

### Autowiring

Edict can use autowiring to automatically resolve dependencies and instantiate your classes:

```php
class MyClass
{
}

$edict->get(MyClass::class); // Instance of MyClass, no configuration needed
```

Edict will inject any dependency for you, as long as it can be resolved:

```php
class AnotherClass
{
    public function __construct(MyClass $class) { /*...*/ }
}

$edict->get(AnotherClass::class); // Instance of AnotherClass
```

You can disable/enable autowiring anytime you want. Any class that has already been autowired will still be accessible.

```php
$edict->disableAutowiring();
$edict->enableAutowiring();
```

### PHP Attributes

Some parameters cannot be automatically resolved. You can either [create your own entry](#using-the-container-inside-an-entry) to pass the correct values to the constructor, or you can use a PHP attribute to tell Edict which entry to use:

```php
use IngeniozIT\Edict\Inject;

class MyClass
{
    public function __construct(
        #[Inject('entryToInject')] int $value
    ) {
        /*...*/
    }
}

$edict->set('entryToInject', value(42));

$edict->get(MyClass::class); // Instance of MyClass with $value = 42
```

## Full documentation

You can list the available features by running

```sh
composer testdox
```

The corresponding code samples are located in the [unit tests file](tests/ContainerTest.php).
