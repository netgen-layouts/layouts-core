<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Status;
use Symfony\Component\Uid\Uuid;

final class CollectionValueResolver extends ValueResolver
{
    public function __construct(
        private CollectionService $collectionService,
    ) {}

    protected function getSourceAttributeNames(): array
    {
        return ['collectionId'];
    }

    protected function getDestinationAttributeName(): string
    {
        return 'collection';
    }

    protected function getSupportedClass(): string
    {
        return Collection::class;
    }

    protected function loadValue(array $parameters): Collection
    {
        /** @var string[]|null $locales */
        $locales = isset($parameters['locale']) ? [$parameters['locale']] : null;

        return match ($parameters['status']) {
            Status::Published => $this->collectionService->loadCollection(Uuid::fromString($parameters['collectionId']), $locales),
            default => $this->collectionService->loadCollectionDraft(Uuid::fromString($parameters['collectionId']), $locales),
        };
    }
}
