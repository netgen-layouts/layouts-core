<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\Doctrine;

use Netgen\Layouts\Tests\Core\Service\CollectionServiceTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class CollectionServiceTest extends CollectionServiceTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
