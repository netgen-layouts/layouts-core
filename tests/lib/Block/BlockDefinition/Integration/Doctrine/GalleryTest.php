<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Integration\Doctrine;

use Netgen\BlockManager\Tests\Block\BlockDefinition\Integration\GalleryTest as BaseGalleryTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\GalleryHandler::__construct
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\GalleryHandler::buildParameters
 */
class GalleryTest extends BaseGalleryTest
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
