<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Doctrine;

use Netgen\BlockManager\Tests\Block\BlockDefinition\GalleryTest as BaseGalleryTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\GalleryHandler::__construct
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\GalleryHandler::buildParameters
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\GalleryHandler::hasCollection
 */
class GalleryTest extends BaseGalleryTest
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
