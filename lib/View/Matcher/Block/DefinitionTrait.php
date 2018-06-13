<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\NullBlockDefinition;

/**
 * This matcher matches if the block has a definition identifier
 * with the value specified in the configuration.
 */
trait DefinitionTrait
{
    public function doMatch(Block $block, array $config)
    {
        $blockDefinition = $block->getDefinition();
        if ($blockDefinition instanceof NullBlockDefinition) {
            return in_array('null', $config, true);
        }

        return in_array($blockDefinition->getIdentifier(), $config, true);
    }
}
