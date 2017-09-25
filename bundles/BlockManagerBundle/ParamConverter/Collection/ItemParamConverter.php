<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

final class ItemParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    public function getSourceAttributeNames()
    {
        return array('itemId');
    }

    public function getDestinationAttributeName()
    {
        return 'item';
    }

    public function getSupportedClass()
    {
        return Item::class;
    }

    public function loadValueObject(array $values)
    {
        if ($values['published']) {
            return $this->collectionService->loadItem($values['itemId']);
        }

        return $this->collectionService->loadItemDraft($values['itemId']);
    }
}
