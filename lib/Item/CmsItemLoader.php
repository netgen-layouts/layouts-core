<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\Item\ItemException;

final class CmsItemLoader implements CmsItemLoaderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\CmsItemBuilderInterface
     */
    private $cmsItemBuilder;

    /**
     * @var \Netgen\BlockManager\Item\ValueLoaderInterface[]
     */
    private $valueLoaders;

    /**
     * @param \Netgen\BlockManager\Item\CmsItemBuilderInterface $cmsItemBuilder
     * @param \Netgen\BlockManager\Item\ValueLoaderInterface[] $valueLoaders
     */
    public function __construct(CmsItemBuilderInterface $cmsItemBuilder, array $valueLoaders = [])
    {
        $this->cmsItemBuilder = $cmsItemBuilder;

        $this->valueLoaders = array_filter(
            $valueLoaders,
            function (ValueLoaderInterface $valueLoader): bool {
                return true;
            }
        );
    }

    public function load($value, string $valueType): CmsItemInterface
    {
        if (!isset($this->valueLoaders[$valueType])) {
            throw ItemException::noValueType($valueType);
        }

        try {
            $loadedValue = $this->valueLoaders[$valueType]->load($value);

            if ($loadedValue === null) {
                return new NullCmsItem($valueType);
            }
        } catch (ItemException $e) {
            // @deprecated For BC with previous versions
            return new NullCmsItem($valueType);
        }

        return $this->cmsItemBuilder->build($loadedValue);
    }

    public function loadByRemoteId($remoteId, string $valueType): CmsItemInterface
    {
        if (!isset($this->valueLoaders[$valueType])) {
            throw ItemException::noValueType($valueType);
        }

        try {
            $loadedValue = $this->valueLoaders[$valueType]->loadByRemoteId($remoteId);

            if ($loadedValue === null) {
                return new NullCmsItem($valueType);
            }
        } catch (ItemException $e) {
            // @deprecated For BC with previous versions
            return new NullCmsItem($valueType);
        }

        return $this->cmsItemBuilder->build($loadedValue);
    }
}
