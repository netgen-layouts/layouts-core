<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Service\CollectionServiceTest as BaseCollectionServiceTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class CollectionServiceTest extends BaseCollectionServiceTest
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
