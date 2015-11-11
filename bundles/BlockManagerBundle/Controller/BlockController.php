<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Values\Page\Block;

class BlockController extends Controller
{
    public function view(Block $block)
    {
        $blockView = $this->buildViewObject($block);

        $blockDefinitionRegistry = $this->get('netgen_block_manager.registry.block_definition');
        $blockDefinition = $blockDefinitionRegistry->getBlockDefinition(
            $block->getDefinitionIdentifier()
        );

        $blockView->addParameters(
            array(
                'block_values' => $blockDefinition->getValues(),
            )
        );

        return $this->renderViewObject($blockView);
    }
}
