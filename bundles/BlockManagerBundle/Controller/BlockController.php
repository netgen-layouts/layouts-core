<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Exception;

class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

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
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(BlockService $blockService, LoggerInterface $logger = null)
    {
        $this->blockService = $blockService;
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
     * Renders the provided block. Used by ESI rendering strategy, so if rendering fails,
     * we log an error and just return an empty response in order not to crash the page.
     *
     * @param int|string $blockId
     * @param string $context
     *
     * @throws \Exception If rendering fails
     *
     * @return \Netgen\BlockManager\View\View\BlockViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function viewBlock($blockId, $context = ViewInterface::CONTEXT_DEFAULT)
    {
        try {
            $block = $this->blockService->loadBlock($blockId);

            return $this->buildView($block, $context);
        } catch (Exception $e) {
            $errorMessage = sprintf('Error rendering a block with ID %d', $blockId);

            $this->logger->error($errorMessage . ': ' . $e->getMessage());

            if ($this->debug) {
                throw $e;
            }

            return new Response();
        }
    }
}
