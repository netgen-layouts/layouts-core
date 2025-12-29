<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Status;
use Symfony\Component\Uid\Uuid;

final class ItemValueResolver extends ValueResolver
{
    public function __construct(
        private CollectionService $collectionService,
    ) {}

    protected function getSourceAttributeNames(): array
    {
        return ['itemId'];
    }

    protected function getDestinationAttributeName(): string
    {
        return 'item';
    }

    protected function getSupportedClass(): string
    {
        return Item::class;
    }

    protected function loadValue(array $parameters): Item
    {
        return match ($parameters['status']) {
            Status::Published => $this->collectionService->loadItem(Uuid::fromString($parameters['itemId'])),
            default => $this->collectionService->loadItemDraft(Uuid::fromString($parameters['itemId'])),
        };
    }
}
