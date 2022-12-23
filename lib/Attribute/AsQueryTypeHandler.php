<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure query type handlers.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsQueryTypeHandler
{
    public string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }
}
