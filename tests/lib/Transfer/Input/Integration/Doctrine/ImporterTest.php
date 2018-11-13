<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Input\Integration\Doctrine;

use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Transfer\Input\Integration\ImporterTest as BaseImporterTest;

final class ImporterTest extends BaseImporterTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
