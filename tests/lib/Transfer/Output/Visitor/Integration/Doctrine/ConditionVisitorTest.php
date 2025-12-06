<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\ConditionVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\ConditionVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConditionVisitor::class)]
final class ConditionVisitorTest extends ConditionVisitorTestBase
{
    use TestCaseTrait;
}
