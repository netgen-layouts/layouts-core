<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\Doctrine;

use Netgen\Layouts\Tests\Core\Service\LayoutServiceTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutServiceTest extends LayoutServiceTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
