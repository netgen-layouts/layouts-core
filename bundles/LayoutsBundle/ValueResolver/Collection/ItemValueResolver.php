<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use Symfony\Component\Uid\Uuid;

final class ItemValueResolver extends ValueResolver
{
    public function __construct(
        private CollectionService $collectionService,
    ) {}

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
        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->collectionService->loadItem(Uuid::fromString($values['itemId'])),
            default => $this->collectionService->loadItemDraft(Uuid::fromString($values['itemId'])),
        };
    }
}
