<?php

namespace Netgen\Bundle\BlockManagerBundle\Renderer;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\API\Values\Page\Block;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Exception;

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
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     * @param \Netgen\BlockManager\View\RendererInterface $viewRenderer
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $fragmentHandler
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        BlockDefinitionRegistryInterface $blockDefinitionRegistry,
        RendererInterface $viewRenderer,
        FragmentHandler $fragmentHandler,
        LoggerInterface $logger = null
    ) {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->viewRenderer = $viewRenderer;
        $this->fragmentHandler = $fragmentHandler;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Sets if debug is enabled or not.
     *
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (bool)$debug;
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
        try {
            $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
                $block->getDefinitionIdentifier()
            );

            return $this->viewRenderer->renderValue(
                $block,
                $context,
                $blockDefinition->getDynamicParameters($block, $parameters) + $parameters
            );
        } catch (Exception $e) {
            if ($this->debug) {
                throw $e;
            }

            // In most cases when rendering a Twig template on frontend
            // we do not want rendering of the block to crash the page,
            // hence we return an empty string and log an error.
            $this->logger->error(
                sprintf(
                    'Error rendering a block with ID %d in layout ID %d and zone identifier %s: %s',
                    $block->getId(),
                    $block->getLayoutId(),
                    $block->getZoneIdentifier(),
                    $e->getMessage()
                ),
                array('ngbm')
            );

            return '';
        }
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
            try {
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
            } catch (Exception $e) {
                if ($this->debug) {
                    throw $e;
                }

                // In most cases when rendering a Twig template on frontend
                // we do not want rendering of the block to crash the page,
                // hence we return an empty string and log an error.
                $this->logger->error(
                    sprintf(
                        'Error rendering a block fragment with ID %d in layout ID %d and zone identifier %s: %s',
                        $block->getId(),
                        $block->getLayoutId(),
                        $block->getZoneIdentifier(),
                        $e->getMessage()
                    ),
                    array('ngbm')
                );

                return '';
            }
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
