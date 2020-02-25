<?php

declare(strict_types=1);

namespace IngeniozIT\Container;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \OutOfBoundsException implements NotFoundExceptionInterface
{
}
