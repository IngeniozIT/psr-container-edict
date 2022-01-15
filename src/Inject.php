<?php

declare(strict_types=1);

namespace IngeniozIT\Edict;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Inject
{
    public function __construct(
        public readonly string $entryId
    ) {
    }

    public function __toString(): string
    {
        return $this->entryId;
    }
}
