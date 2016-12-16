<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Doctrine;

use Netgen\BlockManager\Tests\Block\BlockDefinition\ButtonTest as BaseButtonTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\ButtonHandler::__construct
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\ButtonHandler::buildParameters
 * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildCommonParameters
 */
class ButtonTest extends BaseButtonTest
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
