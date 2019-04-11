<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Tests\Core\Mapper\CollectionMapperTest as BaseCollectionMapperTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class CollectionMapperTest extends BaseCollectionMapperTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
