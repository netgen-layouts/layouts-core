<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\RuleVisitorTestBase;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\RuleVisitor
 */
final class RuleVisitorTest extends RuleVisitorTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
