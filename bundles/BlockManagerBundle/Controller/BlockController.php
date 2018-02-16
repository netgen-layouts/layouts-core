<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Exception;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\View\ViewInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class BlockController extends Controller
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug = false;

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
     * @param string $viewContext
     *
     * @throws \Exception If rendering fails
     *
     * @return \Netgen\BlockManager\View\View\BlockViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function viewBlock(Block $block, $viewContext = ViewInterface::CONTEXT_DEFAULT)
    {
        try {
            return $this->buildView($block, $viewContext);
        } catch (Throwable $e) {
            $errorMessage = sprintf('Error rendering a block with ID %d', $block->getId());

            return new Response($this->handleError($e, $errorMessage));
        } catch (Exception $e) {
            $errorMessage = sprintf('Error rendering a block with ID %d', $block->getId());

            return new Response($this->handleError($e, $errorMessage));
        }
    }

    /**
     * Renders the provided block with the AJAX view.
     *
     * Block rendered with AJAX view is always rendered with a collection
     * which is injected into a block at a certain page.
     *
     * Paging itself of the collection is not handled here, but rather in
     * an event listener triggering when the block is rendered.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param string $viewContext
     *
     * @throws \Exception If rendering fails
     *
     * @return \Netgen\BlockManager\View\View\BlockViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAjaxBlock(Block $block, $collectionIdentifier, $viewContext = ViewInterface::CONTEXT_AJAX)
    {
        try {
            return $this->buildView(
                $block,
                $viewContext,
                array(
                    'collection_identifier' => $collectionIdentifier,
                )
            );
        } catch (Throwable $e) {
            $errorMessage = sprintf(
                'Error rendering an AJAX block with ID %d and collection %s',
                $block->getId(),
                $collectionIdentifier
            );

            return new JsonResponse($this->handleError($e, $errorMessage));
        } catch (Exception $e) {
            $errorMessage = sprintf(
                'Error rendering an AJAX block with ID %d and collection %s',
                $block->getId(),
                $collectionIdentifier
            );

            return new JsonResponse($this->handleError($e, $errorMessage));
        }
    }

    protected function checkPermissions()
    {
    }

    /**
     * Handles the exception based on provided debug flag.
     *
     * @param \Throwable $throwable
     * @param string $errorMessage
     *
     * @todo Refactor out to separate service
     *
     * @deprecated Remove handling of exceptions in PHP 5.6 way
     *
     * @throws \Throwable
     *
     * @return string returns an empty string in non-debug mode
     */
    private function handleError(/* Throwable */ $throwable, $errorMessage)
    {
        $this->logger->critical($errorMessage, array('exception' => $throwable));

        if ($this->debug) {
            throw $throwable;
        }

        return '';
    }
}
