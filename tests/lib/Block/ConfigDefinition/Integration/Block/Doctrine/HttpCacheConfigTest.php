<?php

namespace Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration\Block\Doctrine;

use Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration\Block\HttpCacheConfigTest as BaseHttpCacheConfigTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler::buildParameters
 */
final class HttpCacheConfigTest extends BaseHttpCacheConfigTest
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
