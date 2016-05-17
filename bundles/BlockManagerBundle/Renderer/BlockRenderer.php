<?php

namespace Netgen\Bundle\BlockManagerBundle\Renderer;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\API\Values\Page\Block;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class BlockRenderer implements BlockRendererInterface
{
    const BLOCK_CONTROLLER = 'ngbm_block:viewBlockById';

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    protected $viewRenderer;

    /**
     * @var \Symfony\Component\HttpKernel\Fragment\EsiFragmentRenderer
     */
    protected $fragmentHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     * @param \Netgen\BlockManager\View\RendererInterface $viewRenderer
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $fragmentHandler
     */
    public function __construct(
        BlockDefinitionRegistryInterface $blockDefinitionRegistry,
        RendererInterface $viewRenderer,
        FragmentHandler $fragmentHandler
    ) {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->viewRenderer = $viewRenderer;
        $this->fragmentHandler = $fragmentHandler;
    }

    /**
     * Renders the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $context
     * @param array $parameters
     *
     * @return string
     */
    public function renderBlock(Block $block, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array())
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $block->getDefinitionIdentifier()
        );

        return $this->viewRenderer->renderValue(
            $block,
            $context,
            $blockDefinition->getDynamicParameters($block, $parameters) + $parameters
        );
    }

    /**
     * Renders the block via ESI fragment.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $context
     * @param array $parameters
     *
     * @return string
     */
    public function renderBlockFragment(Block $block, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array())
    {
        if ($this->isBlockCacheable($block)) {
            return $this->fragmentHandler->render(
                new ControllerReference(
                    self::BLOCK_CONTROLLER,
                    array(
                        'blockId' => $block->getId(),
                        'context' => $context,
                        'parameters' => $parameters,
                    )
                ),
                'esi'
            );
        }

        return $this->renderBlock($block, $context, $parameters);
    }

    /**
     * Returns if the block instance is cacheable.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return bool
     */
    protected function isBlockCacheable(Block $block)
    {
        return false;
    }
}
