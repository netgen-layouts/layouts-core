<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

final class QueryParamConverter extends ParamConverter
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
        return ['queryId'];
    }

    public function getDestinationAttributeName()
    {
        return 'query';
    }

    public function getSupportedClass()
    {
        return Query::class;
    }

    public function loadValue(array $values)
    {
        $locales = isset($values['locale']) ? [$values['locale']] : null;

        if ($values['published']) {
            return $this->collectionService->loadQuery($values['queryId'], $locales);
        }

        return $this->collectionService->loadQueryDraft($values['queryId'], $locales);
    }
}
