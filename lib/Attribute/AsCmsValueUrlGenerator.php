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
    public function __construct(
        public private(set) string $valueType,
    ) {}
}
