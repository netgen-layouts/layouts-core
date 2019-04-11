<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Value;

final class CollectionParamConverter extends ParamConverter
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

    public function loadValue(array $values): Value
    {
        /** @var string[] $locales */
        $locales = isset($values['locale']) ? [$values['locale']] : null;

        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->collectionService->loadCollection($values['collectionId'], $locales);
        }

        return $this->collectionService->loadCollectionDraft($values['collectionId'], $locales);
    }
}
