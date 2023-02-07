<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder\Doctrine;

use Netgen\Layouts\Tests\Core\StructBuilder\BlockStructBuilderTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class BlockStructBuilderTest extends BlockStructBuilderTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
