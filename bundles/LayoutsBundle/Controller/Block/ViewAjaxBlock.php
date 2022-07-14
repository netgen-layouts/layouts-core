<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Error\ErrorHandlerInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function sprintf;

final class ViewAjaxBlock extends AbstractController
{
    private ErrorHandlerInterface $errorHandler;

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
     * @return \Netgen\Layouts\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, string $collectionIdentifier, string $viewContext = ViewInterface::CONTEXT_AJAX)
    {
        try {
            return $this->buildView(
                $block,
                $viewContext,
                [
                    'collection_identifier' => $collectionIdentifier,
                ],
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering an AJAX block with UUID %s and collection %s',
                $block->getId()->toString(),
                $collectionIdentifier,
            );

            $this->errorHandler->handleError($t, $message);
        }

        return new Response();
    }
}
