<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Core\Service\Doctrine\TestCase;
use Netgen\BlockManager\Tests\Core\Service\Mapper\CollectionMapperTest as BaseCollectionMapperTest;

class CollectionMapperTest extends BaseCollectionMapperTest
{
    use TestCase;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->preparePersistence();

        parent::setUp();
    }
}
