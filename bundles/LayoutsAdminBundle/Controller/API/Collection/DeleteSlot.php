<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use Symfony\Component\HttpFoundation\Response;

final class DeleteSlot extends AbstractController
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Deletes the slot.
     */
    public function __invoke(Slot $slot): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $this->collectionService->deleteSlot($slot);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
