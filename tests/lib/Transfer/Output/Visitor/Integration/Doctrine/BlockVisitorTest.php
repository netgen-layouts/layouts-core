<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\BlockVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\BlockVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BlockVisitor::class)]
final class BlockVisitorTest extends BlockVisitorTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
