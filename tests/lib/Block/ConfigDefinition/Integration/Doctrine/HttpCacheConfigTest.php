<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration\Doctrine;

use Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration\HttpCacheConfigTest as BaseHttpCacheConfigTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler::buildParameters
 */
final class HttpCacheConfigTest extends BaseHttpCacheConfigTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the persistence handler used in tests.
     */
    public function preparePersistence(): void
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}
