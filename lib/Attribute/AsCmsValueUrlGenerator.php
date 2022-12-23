<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure value url generators.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsCmsValueUrlGenerator
{
    public string $valueType;

    public function __construct(string $valueType)
    {
        $this->valueType = $valueType;
    }
}
