<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure parameter type form mappers.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsParameterTypeFormMapper
{
    public string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }
}
