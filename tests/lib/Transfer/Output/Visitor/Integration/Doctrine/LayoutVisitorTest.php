<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\LayoutVisitorTest as BaseLayoutVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\LayoutVisitor
 */
final class LayoutVisitorTest extends BaseLayoutVisitorTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
