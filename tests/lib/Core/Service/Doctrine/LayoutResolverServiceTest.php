<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\Doctrine;

use Netgen\Layouts\Tests\Core\Service\LayoutResolverServiceTest as BaseLayoutResolverServiceTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class LayoutResolverServiceTest extends BaseLayoutResolverServiceTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
