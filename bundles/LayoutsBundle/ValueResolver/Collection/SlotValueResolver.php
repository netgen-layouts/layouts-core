<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use Ramsey\Uuid\Uuid;

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

    public function loadValue(array $values): Slot
    {
        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->collectionService->loadSlot(Uuid::fromString($values['slotId'])),
            default => $this->collectionService->loadSlotDraft(Uuid::fromString($values['slotId'])),
        };
    }
}
