<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Tests\Core\Mapper\CollectionMapperTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class CollectionMapperTest extends CollectionMapperTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
