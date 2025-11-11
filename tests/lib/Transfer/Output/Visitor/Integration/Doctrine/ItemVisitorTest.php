<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\ItemVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\ItemVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ItemVisitor::class)]
final class ItemVisitorTest extends ItemVisitorTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
