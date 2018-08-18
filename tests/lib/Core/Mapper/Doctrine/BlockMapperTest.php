<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Mapper\Doctrine;

use Netgen\BlockManager\Tests\Core\Mapper\BlockMapperTest as BaseBlockMapperTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class BlockMapperTest extends BaseBlockMapperTest
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