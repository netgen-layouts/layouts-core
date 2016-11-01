<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\Bundle\BlockManagerBundle\Exception\RenderingFailedException;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Exception;

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
        $this->debug = (bool)$debug;
    }

    /**
     * Renders the provided block. Used by ESI rendering strategy, so if rendering fails,
     * we log an error and just return an empty response in order not to crash the page.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     * @param string $context
     *
     * @throws \Netgen\Bundle\BlockManagerBundle\Exception\RenderingFailedException If rendering fails
     *
     * @return \Netgen\BlockManager\View\View\BlockViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function viewBlock(Block $block, array $parameters = array(), $context = ViewInterface::CONTEXT_DEFAULT)
    {
        try {
            return $this->buildView($block, $parameters, $context);
        } catch (Exception $e) {
            $errorMessage = sprintf('Error rendering a block with ID %d', $block->getId());

            $this->logger->error($errorMessage . ': ' . $e->getMessage());

            if ($this->debug) {
                throw new RenderingFailedException($errorMessage, 0, $e);
            }

            return new Response();
        }
    }
}
