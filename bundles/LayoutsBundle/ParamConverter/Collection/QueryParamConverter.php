<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use Ramsey\Uuid\Uuid;

final class QueryParamConverter extends ParamConverter
{
    private CollectionService $collectionService;

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

    public function loadValue(array $values): Query
    {
        /** @var string[] $locales */
        $locales = isset($values['locale']) ? [$values['locale']] : null;

        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->collectionService->loadQuery(Uuid::fromString($values['queryId']), $locales);
        }

        return $this->collectionService->loadQueryDraft(Uuid::fromString($values['queryId']), $locales);
    }
}
