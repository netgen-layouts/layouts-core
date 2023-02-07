<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\RuleGroupVisitorTestBase;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\RuleGroupVisitor
 */
final class RuleGroupVisitorTest extends RuleGroupVisitorTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
