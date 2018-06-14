<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveItem extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Moves the item inside the collection.
     */
    public function __invoke(Item $item, Request $request): Response
    {
        $this->collectionService->moveItem(
            $item,
            $request->attributes->get('data')->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
