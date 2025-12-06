<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\ZoneVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\ZoneVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ZoneVisitor::class)]
final class ZoneVisitorTest extends ZoneVisitorTestBase
{
    use TestCaseTrait;
}
