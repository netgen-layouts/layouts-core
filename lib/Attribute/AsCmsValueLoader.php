<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure value loaders.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsCmsValueLoader
{
    public string $valueType;

    public function __construct(string $valueType)
    {
        $this->valueType = $valueType;
    }
}
