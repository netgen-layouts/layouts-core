<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure block definition handlers.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsBlockDefinitionHandler
{
    public string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }
}
