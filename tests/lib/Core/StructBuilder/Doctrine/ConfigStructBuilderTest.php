<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\StructBuilder\Doctrine;

use Netgen\BlockManager\Tests\Core\StructBuilder\ConfigStructBuilderTest as BaseConfigStructBuilderTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class ConfigStructBuilderTest extends BaseConfigStructBuilderTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the prerequisites for using services in tests.
     */
    protected function preparePersistence(): void
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}
