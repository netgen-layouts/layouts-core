<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Core\Service\Mapper\LayoutMapperTest as BaseLayoutMapperTest;

class LayoutMapperTest extends BaseLayoutMapperTest
{
    use TestCaseTrait;

    /**
     * Prepares the prerequisites for using services in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }

    public function tearDown()
    {
        $this->closeDatabase();
    }
}
