<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\LayoutVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\LayoutVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LayoutVisitor::class)]
final class LayoutVisitorTest extends LayoutVisitorTestBase
{
    use TestCaseTrait;
}
