<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Tests\Core\Mapper\BlockMapperTest as BaseBlockMapperTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class BlockMapperTest extends BaseBlockMapperTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
