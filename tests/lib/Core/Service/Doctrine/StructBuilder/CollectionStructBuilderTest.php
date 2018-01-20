<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\StructBuilder;

use Netgen\BlockManager\Tests\Core\Service\StructBuilder\CollectionStructBuilderTest as BaseCollectionStructBuilderTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class CollectionStructBuilderTest extends BaseCollectionStructBuilderTest
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
