<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\RuleGroupVisitorTest as BaseRuleGroupVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\RuleGroupVisitor
 */
final class RuleGroupVisitorTest extends BaseRuleGroupVisitorTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
