<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Exception;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Error\ErrorHandlerInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\Error\ErrorHandlerInterface
     */
    private $errorHandler;

    public function __construct(ErrorHandlerInterface $errorHandler)
    {
        $this->errorHandler = $errorHandler;
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
        } catch (Throwable $t) {
            $message = sprintf('Error rendering a block with ID %d', $block->getId());

            $this->errorHandler->handleError($t, $message);
        } catch (Exception $e) {
            $message = sprintf('Error rendering a block with ID %d', $block->getId());

            $this->errorHandler->handleError($e, $message);
        }

        return new Response();
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
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering an AJAX block with ID %d and collection %s',
                $block->getId(),
                $collectionIdentifier
            );

            $this->errorHandler->handleError($t, $message);
        } catch (Exception $e) {
            $message = sprintf(
                'Error rendering an AJAX block with ID %d and collection %s',
                $block->getId(),
                $collectionIdentifier
            );

            $this->errorHandler->handleError($e, $message);
        }

        return new JsonResponse();
    }

    protected function checkPermissions()
    {
    }
}
