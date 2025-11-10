<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use Ramsey\Uuid\Uuid;

final class ItemValueResolver extends ValueResolver
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    public function getSourceAttributeNames(): array
    {
        return ['itemId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'item';
    }

    public function getSupportedClass(): string
    {
        return Item::class;
    }

    public function loadValue(array $values): Item
    {
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->collectionService->loadItem(Uuid::fromString($values['itemId']));
        }

        return $this->collectionService->loadItemDraft(Uuid::fromString($values['itemId']));
    }
}
