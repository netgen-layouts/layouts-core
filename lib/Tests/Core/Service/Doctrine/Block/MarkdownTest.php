<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Block;

use Netgen\BlockManager\Tests\Core\Service\Block\MarkdownTest as BaseMarkdownTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\MarkdownHandler::__construct
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\MarkdownHandler::buildParameters
 */
class MarkdownTest extends BaseMarkdownTest
{
    use TestCaseTrait;

    /**
     * Prepares the persistence handler used in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }

    public function tearDown()
    {
        $this->closeDatabase();
    }
}
