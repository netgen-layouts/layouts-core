<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Config;

interface ConfigDefinitionAwareInterface
{
    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions(): array;
}
