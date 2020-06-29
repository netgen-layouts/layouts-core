<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use Ramsey\Uuid\Uuid;

final class ItemParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\Layouts\API\Service\CollectionService
     */
    private $collectionService;

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

    public function loadValue(array $values): object
    {
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->collectionService->loadItem(Uuid::fromString($values['itemId']));
        }

        return $this->collectionService->loadItemDraft(Uuid::fromString($values['itemId']));
    }
}
