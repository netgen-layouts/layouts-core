<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\Doctrine;

use Netgen\Layouts\Tests\Core\Service\BlockServiceTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class BlockServiceTest extends BlockServiceTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
