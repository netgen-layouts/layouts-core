<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Result\ContextualQueryRunner;
use Netgen\Layouts\Collection\Result\UnknownItem;
use PHPUnit\Framework\TestCase;

use function intdiv;

use const PHP_INT_MAX;

final class ContextualQueryRunnerTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Collection\Result\ContextualQueryRunner::count
     * @covers \Netgen\Layouts\Collection\Result\ContextualQueryRunner::runQuery
     */
    public function testRunner(): void
    {
        $queryRunner = new ContextualQueryRunner();

        $values = [...$queryRunner->runQuery(new Query(), 0, 5)];

        self::assertCount(5, $values);
        self::assertContainsOnlyInstancesOf(UnknownItem::class, $values);
        self::assertSame(intdiv(PHP_INT_MAX - 1, 2), $queryRunner->count(new Query()));
    }
}
