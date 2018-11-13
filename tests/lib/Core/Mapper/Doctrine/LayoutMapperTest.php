<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Mapper\Doctrine;

use Netgen\BlockManager\Tests\Core\Mapper\LayoutMapperTest as BaseLayoutMapperTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutMapperTest extends BaseLayoutMapperTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
