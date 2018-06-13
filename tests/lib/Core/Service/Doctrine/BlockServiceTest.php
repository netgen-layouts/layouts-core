<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Service\BlockServiceTest as BaseBlockServiceTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class BlockServiceTest extends BaseBlockServiceTest
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
