<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder\Doctrine;

use Netgen\Layouts\Tests\Core\StructBuilder\LayoutStructBuilderTest as BaseLayoutStructBuilderTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutStructBuilderTest extends BaseLayoutStructBuilderTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
