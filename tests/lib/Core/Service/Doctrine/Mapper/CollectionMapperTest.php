<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Core\Service\Mapper\CollectionMapperTest as BaseCollectionMapperTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class CollectionMapperTest extends BaseCollectionMapperTest
{
    use TestCaseTrait;

    public function tearDown()
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the prerequisites for using services in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}
