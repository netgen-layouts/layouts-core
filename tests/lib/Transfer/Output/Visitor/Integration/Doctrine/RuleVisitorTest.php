<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\RuleVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\RuleVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RuleVisitor::class)]
final class RuleVisitorTest extends RuleVisitorTestBase
{
    use TestCaseTrait;
}
