<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

use Netgen\Layouts\Exception\Item\ItemException;

final class CmsItemLoader implements CmsItemLoaderInterface
{
    /**
     * @var \Netgen\Layouts\Item\CmsItemBuilderInterface
     */
    private $cmsItemBuilder;

    /**
     * @var \Netgen\Layouts\Item\ValueLoaderInterface[]
     */
    private $valueLoaders;

    /**
     * @param \Netgen\Layouts\Item\CmsItemBuilderInterface $cmsItemBuilder
     * @param \Netgen\Layouts\Item\ValueLoaderInterface[] $valueLoaders
     */
    public function __construct(CmsItemBuilderInterface $cmsItemBuilder, array $valueLoaders)
    {
        $this->cmsItemBuilder = $cmsItemBuilder;

        $this->valueLoaders = array_filter(
            $valueLoaders,
            static function (ValueLoaderInterface $valueLoader): bool {
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
