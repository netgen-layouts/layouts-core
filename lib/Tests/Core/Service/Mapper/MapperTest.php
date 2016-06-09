<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use PHPUnit\Framework\TestCase;

abstract class MapperTest extends TestCase
{
    /**
     * Creates a layout mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    abstract protected function createLayoutMapper();

    /**
     * Creates a block mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    abstract protected function createBlockMapper();

    /**
     * Creates a collection mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    abstract protected function createCollectionMapper();

    /**
     * Creates a layout resolver mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    abstract protected function createLayoutResolverMapper();
}
