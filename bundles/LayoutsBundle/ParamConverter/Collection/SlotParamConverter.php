<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use Ramsey\Uuid\Uuid;

final class SlotParamConverter extends ParamConverter
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

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
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->collectionService->loadSlot(Uuid::fromString($values['slotId']));
        }

        return $this->collectionService->loadSlotDraft(Uuid::fromString($values['slotId']));
    }
}
