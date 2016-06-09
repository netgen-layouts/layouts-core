<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Core\Service\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Core\Service\Mapper\CollectionMapperTest as BaseCollectionMapperTest;

class CollectionMapperTest extends BaseCollectionMapperTest
{
    use TestCaseTrait;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->preparePersistence();

        parent::setUp();
    }
}
