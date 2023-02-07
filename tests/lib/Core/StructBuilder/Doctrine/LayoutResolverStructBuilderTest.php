<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder\Doctrine;

use Netgen\Layouts\Tests\Core\StructBuilder\LayoutResolverStructBuilderTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutResolverStructBuilderTest extends LayoutResolverStructBuilderTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
