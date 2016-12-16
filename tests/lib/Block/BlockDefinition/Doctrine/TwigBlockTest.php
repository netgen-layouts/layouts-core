<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Doctrine;

use Netgen\BlockManager\Tests\Block\BlockDefinition\TwigBlockTest as BaseTwigBlockTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler::buildParameters
 * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildCommonParameters
 */
class TwigBlockTest extends BaseTwigBlockTest
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
