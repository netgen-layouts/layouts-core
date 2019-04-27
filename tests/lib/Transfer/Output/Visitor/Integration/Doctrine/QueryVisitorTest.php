<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\QueryVisitorTest as BaseQueryVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\QueryVisitor
 */
final class QueryVisitorTest extends BaseQueryVisitorTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
