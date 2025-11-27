<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\Config\ConfigDefinitionAwareTrait;

final class NullItemDefinition implements ItemDefinitionInterface
{
    use ConfigDefinitionAwareTrait;

    public function __construct(
        private(set) string $valueType,
    ) {}
}
