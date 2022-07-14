<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\NullBlockDefinition;

use function in_array;

/**
 * This matcher matches if the block has a definition identifier
 * with the value specified in the configuration.
 */
trait DefinitionTrait
{
    /**
     * @param mixed[] $config
     */
    private function doMatch(Block $block, array $config): bool
    {
        $blockDefinition = $block->getDefinition();
        if ($blockDefinition instanceof NullBlockDefinition) {
            return in_array('null', $config, true);
        }

        return in_array($blockDefinition->getIdentifier(), $config, true);
    }
}
