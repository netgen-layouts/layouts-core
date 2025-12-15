<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Status;
use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use Symfony\Component\Uid\Uuid;

final class SlotValueResolver extends ValueResolver
{
    public function __construct(
        private CollectionService $collectionService,
    ) {}

    public function getSourceAttributeNames(): array
    {
        return ['slotId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'slot';
    }

    public function getSupportedClass(): string
    {
        return Slot::class;
    }

    public function loadValue(array $parameters): Slot
    {
        return match ($parameters['status']) {
            Status::Published => $this->collectionService->loadSlot(Uuid::fromString($parameters['slotId'])),
            default => $this->collectionService->loadSlotDraft(Uuid::fromString($parameters['slotId'])),
        };
    }
}
