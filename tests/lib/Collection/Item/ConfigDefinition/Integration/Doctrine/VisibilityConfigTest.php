<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Item\ConfigDefinition\Integration\Doctrine;

use Netgen\BlockManager\Tests\Collection\Item\ConfigDefinition\Integration\VisibilityConfigTest as BaseVisibilityConfigTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Collection\Item\ConfigDefinition\Handler\VisibilityConfigHandler::buildParameters
 */
final class VisibilityConfigTest extends BaseVisibilityConfigTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the persistence handler used in tests.
     */
    public function preparePersistence(): void
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}
