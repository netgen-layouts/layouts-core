<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use Ramsey\Uuid\Uuid;

final class QueryValueResolver extends ValueResolver
{
    public function __construct(
        private CollectionService $collectionService,
    ) {}

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

        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->collectionService->loadQuery(Uuid::fromString($values['queryId']), $locales),
            default => $this->collectionService->loadQueryDraft(Uuid::fromString($values['queryId']), $locales),
        };
    }
}
