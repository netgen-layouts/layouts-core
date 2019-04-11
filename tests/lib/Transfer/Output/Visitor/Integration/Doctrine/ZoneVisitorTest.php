<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\ZoneVisitorTest as BaseZoneVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\ZoneVisitor
 */
final class ZoneVisitorTest extends BaseZoneVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
