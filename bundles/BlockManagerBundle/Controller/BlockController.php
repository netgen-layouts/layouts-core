<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Values\Page\Block;

class BlockController extends Controller
{
    /**
     * Renders the provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(Block $block, array $parameters = array())
    {
        $blockView = $this->buildViewObject($block);

        $blockDefinition = $this->getBlockDefinition($block->getDefinitionIdentifier());

        $blockView->addParameters($parameters);
        $blockView->addParameters($blockDefinition->getDynamicParameters($block, $parameters));

        return $blockView;
    }
}
