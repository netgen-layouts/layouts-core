<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Core\Service\Mapper\LayoutResolverMapperTest as BaseLayoutResolverMapperTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutResolverMapperTest extends BaseLayoutResolverMapperTest
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
