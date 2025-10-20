<?php

namespace IngeniozIt\Edict;

use OutOfBoundsException;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends OutOfBoundsException implements NotFoundExceptionInterface
{
}
