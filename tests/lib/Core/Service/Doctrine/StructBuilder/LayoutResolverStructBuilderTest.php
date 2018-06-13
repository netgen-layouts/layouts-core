<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\StructBuilder;

use Netgen\BlockManager\Tests\Core\Service\StructBuilder\LayoutResolverStructBuilderTest as BaseLayoutResolverStructBuilderTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutResolverStructBuilderTest extends BaseLayoutResolverStructBuilderTest
{
    use TestCaseTrait;

    public function tearDown()
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the prerequisites for using services in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}
