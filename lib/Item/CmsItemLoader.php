<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

use Netgen\Layouts\Exception\Item\ItemException;
use Psr\Container\ContainerInterface;

final class CmsItemLoader implements CmsItemLoaderInterface
{
    private CmsItemBuilderInterface $cmsItemBuilder;

    private ContainerInterface $valueLoaders;

    public function __construct(CmsItemBuilderInterface $cmsItemBuilder, ContainerInterface $valueLoaders)
    {
        $this->cmsItemBuilder = $cmsItemBuilder;
        $this->valueLoaders = $valueLoaders;
    }

    public function load($id, string $valueType): CmsItemInterface
    {
        $valueLoader = $this->getValueLoader($valueType);
        $value = $valueLoader->load($id);

        if ($value === null) {
            return new NullCmsItem($valueType);
        }

        return $this->cmsItemBuilder->build($value);
    }

    public function loadByRemoteId($remoteId, string $valueType): CmsItemInterface
    {
        $valueLoader = $this->getValueLoader($valueType);
        $value = $valueLoader->loadByRemoteId($remoteId);

        if ($value === null) {
            return new NullCmsItem($valueType);
        }

        return $this->cmsItemBuilder->build($value);
    }

    /**
     * Returns the value loader for provided value type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If the value loader does not exist or is not of correct type
     */
    private function getValueLoader(string $valueType): ValueLoaderInterface
    {
        if (!$this->valueLoaders->has($valueType)) {
            throw ItemException::noValueLoader($valueType);
        }

        $valueLoader = $this->valueLoaders->get($valueType);
        if (!$valueLoader instanceof ValueLoaderInterface) {
            throw ItemException::noValueLoader($valueType);
        }

        return $valueLoader;
    }
}
