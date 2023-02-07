<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\TargetVisitorTestBase;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\TargetVisitor
 */
final class TargetVisitorTest extends TargetVisitorTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
