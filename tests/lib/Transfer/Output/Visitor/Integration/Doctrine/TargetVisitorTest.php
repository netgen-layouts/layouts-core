<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\TargetVisitorTest as BaseTargetVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\TargetVisitor
 */
final class TargetVisitorTest extends BaseTargetVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
