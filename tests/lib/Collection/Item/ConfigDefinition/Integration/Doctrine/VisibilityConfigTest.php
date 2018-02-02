<?php

namespace Netgen\BlockManager\Tests\Collection\Item\ConfigDefinition\Integration\Doctrine;

use Netgen\BlockManager\Tests\Collection\Item\ConfigDefinition\Integration\VisibilityConfigTest as BaseVisibilityConfigTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Collection\Item\ConfigDefinition\Handler\VisibilityConfigHandler::buildParameters
 */
final class VisibilityConfigTest extends BaseVisibilityConfigTest
{
    use TestCaseTrait;

    public function tearDown()
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the persistence handler used in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}
