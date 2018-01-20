<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\StructBuilder;

use Netgen\BlockManager\Tests\Core\Service\StructBuilder\LayoutStructBuilderTest as BaseLayoutStructBuilderTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutStructBuilderTest extends BaseLayoutStructBuilderTest
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
