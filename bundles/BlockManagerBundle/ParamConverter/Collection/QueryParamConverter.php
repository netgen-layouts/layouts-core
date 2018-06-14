<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Value;
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

    public function getSourceAttributeNames(): array
    {
        return ['queryId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'query';
    }

    public function getSupportedClass(): string
    {
        return Query::class;
    }

    public function loadValue(array $values): Value
    {
        $locales = isset($values['locale']) ? [$values['locale']] : null;

        if ($values['status'] === self::$statusPublished) {
            return $this->collectionService->loadQuery($values['queryId'], $locales);
        }

        return $this->collectionService->loadQueryDraft($values['queryId'], $locales);
    }
}
