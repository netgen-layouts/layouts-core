<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Configuration;

use Netgen\Layouts\API\Values\Block\Block;

interface ConfigProviderInterface
{
    /**
     * Provides the list of view types for the block definition.
     *
     * @return \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType[]
     */
    public function provideViewTypes(?Block $block = null): array;
}
