<?php

namespace Netgen\BlockManager\Tests\Config\ConfigDefinition\Integration\Block\Doctrine;

use Netgen\BlockManager\Tests\Config\ConfigDefinition\Integration\Block\HttpCacheConfigTest as BaseHttpCacheConfigTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Config\ConfigDefinition\Block\HttpCacheConfigHandler::__construct
 * @covers \Netgen\BlockManager\Config\ConfigDefinition\Block\HttpCacheConfigHandler::buildParameters
 */
class HttpCacheConfigTest extends BaseHttpCacheConfigTest
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
