<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Core\Service\Mapper\BlockMapperTest as BaseBlockMapperTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class BlockMapperTest extends BaseBlockMapperTest
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
