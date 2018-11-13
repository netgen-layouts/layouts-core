<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Mapper\Doctrine;

use Netgen\BlockManager\Tests\Core\Mapper\CollectionMapperTest as BaseCollectionMapperTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class CollectionMapperTest extends BaseCollectionMapperTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
