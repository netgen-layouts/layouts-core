<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Symfony\Component\Uid\Uuid;

final class CollectionValueResolver extends ValueResolver
{
    public function __construct(
        private CollectionService $collectionService,
    ) {}

    public function getSourceAttributeNames(): array
    {
        return ['collectionId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'collection';
    }

    public function getSupportedClass(): string
    {
        return Collection::class;
    }

    public function loadValue(array $values): Collection
    {
        /** @var string[]|null $locales */
        $locales = isset($values['locale']) ? [$values['locale']] : null;

        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->collectionService->loadCollection(Uuid::fromString($values['collectionId']), $locales),
            default => $this->collectionService->loadCollectionDraft(Uuid::fromString($values['collectionId']), $locales),
        };
    }
}
