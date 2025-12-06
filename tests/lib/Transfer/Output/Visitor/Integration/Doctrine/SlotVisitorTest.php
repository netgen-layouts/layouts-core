<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\SlotVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\SlotVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SlotVisitor::class)]
final class SlotVisitorTest extends SlotVisitorTestBase
{
    use TestCaseTrait;
}
