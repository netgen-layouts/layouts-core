<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Exception;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\View\ViewInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends Controller
{
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
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Sets if debug is enabled or not.
     *
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;
    }

    /**
     * Renders the provided block. Used by ESI rendering strategy, so if rendering fails,
     * we log an error and just return an empty response in order not to crash the page.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $context
     *
     * @throws \Exception If rendering fails
     *
     * @return \Netgen\BlockManager\View\View\BlockViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function viewBlock(Block $block, $context = ViewInterface::CONTEXT_DEFAULT)
    {
        try {
            return $this->buildView($block, $context);
        } catch (Exception $e) {
            $errorMessage = sprintf('Error rendering a block with ID %d', $block->getId());

            $this->logger->error($errorMessage . ': ' . $e->getMessage());

            if ($this->debug) {
                throw $e;
            }

            return new Response();
        }
    }
}
