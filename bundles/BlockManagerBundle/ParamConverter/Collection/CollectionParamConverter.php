<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

class CollectionParamConverter extends ParamConverter
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
        return array('collectionId');
    }

    public function getDestinationAttributeName()
    {
        return 'collection';
    }

    public function getSupportedClass()
    {
        return Collection::class;
    }

    public function loadValueObject(array $values)
    {
        $locale = isset($values['locale']) ? $values['locale'] : null;

        if ($values['published']) {
            return $this->collectionService->loadCollection($values['collectionId'], $locale);
        }

        return $this->collectionService->loadCollectionDraft($values['collectionId'], $locale);
    }
}
