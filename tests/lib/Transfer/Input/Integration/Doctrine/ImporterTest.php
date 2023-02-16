<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Input\Integration\ImporterTestBase;

final class ImporterTest extends ImporterTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
