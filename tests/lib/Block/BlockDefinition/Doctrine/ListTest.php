<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Doctrine;

use Netgen\BlockManager\Tests\Block\BlockDefinition\ListTest as BaseListTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\ListHandler::__construct
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\ListHandler::buildParameters
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\ListHandler::hasCollection
 */
class ListTest extends BaseListTest
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
