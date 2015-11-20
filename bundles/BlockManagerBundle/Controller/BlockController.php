<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Values\Page\Block;

class BlockController extends Controller
{
    /**
     * Renders the provided block
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(Block $block)
    {
        $blockView = $this->buildViewObject($block);

        $blockDefinitionRegistry = $this->get('netgen_block_manager.block_definition.registry');
        $blockDefinition = $blockDefinitionRegistry->getBlockDefinition(
            $block->getDefinitionIdentifier()
        );

        $blockView->addParameters($blockDefinition->getValues($block));

        return $this->renderViewObject($blockView);
    }
}
