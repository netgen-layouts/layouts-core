<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\SlotVisitorTest as BaseSlotVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\SlotVisitor
 */
final class SlotVisitorTest extends BaseSlotVisitorTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
