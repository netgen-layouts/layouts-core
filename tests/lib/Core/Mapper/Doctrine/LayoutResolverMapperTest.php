<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Tests\Core\Mapper\LayoutResolverMapperTest as BaseLayoutResolverMapperTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutResolverMapperTest extends BaseLayoutResolverMapperTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
