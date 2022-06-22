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

To learn more about how to use Edict, please refer to the [wiki](https://github.com/IngeniozIT/psr-container-edict/wiki).

## Full documentation

You can list the available features by running

```sh
composer testdox
```

The corresponding code samples are located in the [unit tests file](tests/ContainerTest.php).
