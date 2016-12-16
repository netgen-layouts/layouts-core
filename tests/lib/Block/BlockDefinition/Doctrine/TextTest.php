<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Doctrine;

use Netgen\BlockManager\Tests\Block\BlockDefinition\TextTest as BaseTextTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TextHandler::buildParameters
 * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildCommonParameters
 */
class TextTest extends BaseTextTest
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
