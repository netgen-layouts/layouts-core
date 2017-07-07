<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Integration\Doctrine;

use Netgen\BlockManager\Tests\Block\BlockDefinition\Integration\FullViewTest as BaseFullViewTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\FullViewHandler::buildParameters
 */
class FullViewTest extends BaseFullViewTest
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
