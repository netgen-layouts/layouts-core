<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Symfony\Component\HttpFoundation\Response;

final class DeleteItems extends AbstractController
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Deletes all items from provided collection.
     */
    public function __invoke(Collection $collection): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $this->collectionService->deleteItems($collection);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
