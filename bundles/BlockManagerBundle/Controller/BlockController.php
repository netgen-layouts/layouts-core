<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Renderer\BlockRendererInterface;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRendererInterface
     */
    protected $blockRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRendererInterface $blockRenderer
     */
    public function __construct(BlockService $blockService, BlockRendererInterface $blockRenderer)
    {
        $this->blockService = $blockService;
        $this->blockRenderer = $blockRenderer;
    }

    /**
     * Renders the provided block.
     *
     * @param string $blockId
     * @param array $parameters
     * @param string $context
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewBlockById($blockId, array $parameters = array(), $context = ViewInterface::CONTEXT_VIEW)
    {
        $block = $this->blockService->loadBlock($blockId, Layout::STATUS_PUBLISHED);

        $response = new Response();
        $response->setContent(
            $this->blockRenderer->renderBlock($block, $context, $parameters)
        );

        return $response;
    }
}
