<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Core\Service\LayoutResolverServiceTest as BaseLayoutResolverServiceTest;

class LayoutResolverServiceTest extends BaseLayoutResolverServiceTest
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
