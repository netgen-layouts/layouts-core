<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\RuleGroupVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\RuleGroupVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RuleGroupVisitor::class)]
final class RuleGroupVisitorTest extends RuleGroupVisitorTestBase
{
    use TestCaseTrait;
}
