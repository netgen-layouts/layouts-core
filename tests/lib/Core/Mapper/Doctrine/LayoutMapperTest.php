<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Tests\Core\Mapper\LayoutMapperTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutMapperTest extends LayoutMapperTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
