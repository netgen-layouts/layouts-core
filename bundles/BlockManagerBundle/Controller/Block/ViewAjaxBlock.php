<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Error\ErrorHandlerInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ViewAjaxBlock extends Controller
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
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, $collectionIdentifier, $viewContext = ViewInterface::CONTEXT_AJAX)
    {
        try {
            return $this->buildView(
                $block,
                $viewContext,
                [
                    'collection_identifier' => $collectionIdentifier,
                ]
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering an AJAX block with ID %d and collection %s',
                $block->getId(),
                $collectionIdentifier
            );

            $this->errorHandler->handleError($t, $message);
        }

        return new Response();
    }

    protected function checkPermissions()
    {
    }
}
