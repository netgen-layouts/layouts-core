<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Status;
use Symfony\Component\Uid\Uuid;

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

    public function loadValue(array $parameters): Query
    {
        /** @var string[]|null $locales */
        $locales = isset($parameters['locale']) ? [$parameters['locale']] : null;

        return match ($parameters['status']) {
            Status::Published => $this->collectionService->loadQuery(Uuid::fromString($parameters['queryId']), $locales),
            default => $this->collectionService->loadQueryDraft(Uuid::fromString($parameters['queryId']), $locales),
        };
    }
}
