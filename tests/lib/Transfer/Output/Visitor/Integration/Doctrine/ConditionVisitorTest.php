<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\ConditionVisitorTest as BaseConditionVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\ConditionVisitor
 */
final class ConditionVisitorTest extends BaseConditionVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
