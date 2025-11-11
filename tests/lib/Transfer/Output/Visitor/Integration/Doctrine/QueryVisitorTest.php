<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\QueryVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\QueryVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(QueryVisitor::class)]
final class QueryVisitorTest extends QueryVisitorTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
