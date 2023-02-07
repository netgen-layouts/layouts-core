<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Tests\Core\Mapper\LayoutResolverMapperTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutResolverMapperTest extends LayoutResolverMapperTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
