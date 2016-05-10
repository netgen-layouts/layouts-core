<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\View\ViewBuilderInterface;

class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     */
    public function __construct(ViewBuilderInterface $viewBuilder)
    {
        $this->viewBuilder = $viewBuilder;
    }

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
        $blockView = $this->viewBuilder->buildView($block);

        $blockDefinition = $this->getBlockDefinition($block->getDefinitionIdentifier());

        $blockView->addParameters($parameters);
        $blockView->addParameters($blockDefinition->getDynamicParameters($block, $parameters));

        return $blockView;
    }
}
