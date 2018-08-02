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
    public function __construct(CmsItemBuilderInterface $cmsItemBuilder, array $valueLoaders)
    {
        $this->cmsItemBuilder = $cmsItemBuilder;

        $this->valueLoaders = array_filter(
            $valueLoaders,
            function (ValueLoaderInterface $valueLoader): bool {
                return true;
            }
        );
    }

    public function load($id, string $valueType): CmsItemInterface
    {
        if (!isset($this->valueLoaders[$valueType])) {
            throw ItemException::noValueType($valueType);
        }

        $value = $this->valueLoaders[$valueType]->load($id);

        if ($value === null) {
            return new NullCmsItem($valueType);
        }

        return $this->cmsItemBuilder->build($value);
    }

    public function loadByRemoteId($remoteId, string $valueType): CmsItemInterface
    {
        if (!isset($this->valueLoaders[$valueType])) {
            throw ItemException::noValueType($valueType);
        }

        $value = $this->valueLoaders[$valueType]->loadByRemoteId($remoteId);

        if ($value === null) {
            return new NullCmsItem($valueType);
        }

        return $this->cmsItemBuilder->build($value);
    }
}
