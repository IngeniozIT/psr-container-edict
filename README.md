# Edict

Easy DI ConTainer is a PSR-11 implementation.

# Basic usage

```php
<?php

use IngeniozIT\Container\Edict;

$container = new Edict();

$container->set('foo', 'bar');

$container->get('foo'); // 'bar'
```
