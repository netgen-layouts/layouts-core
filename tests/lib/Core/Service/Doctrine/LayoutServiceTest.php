<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\Doctrine;

use Netgen\Layouts\Tests\Core\Service\LayoutServiceTest as BaseLayoutServiceTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutServiceTest extends BaseLayoutServiceTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
