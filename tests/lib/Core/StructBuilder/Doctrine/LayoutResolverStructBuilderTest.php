<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\StructBuilder\Doctrine;

use Netgen\BlockManager\Tests\Core\StructBuilder\LayoutResolverStructBuilderTest as BaseLayoutResolverStructBuilderTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutResolverStructBuilderTest extends BaseLayoutResolverStructBuilderTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the prerequisites for using services in tests.
     */
    protected function preparePersistence(): void
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}
