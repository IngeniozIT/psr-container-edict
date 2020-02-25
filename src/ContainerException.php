<?php

declare(strict_types=1);

namespace IngeniozIT\Container;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends \OutOfBoundsException implements ContainerExceptionInterface
{
}
