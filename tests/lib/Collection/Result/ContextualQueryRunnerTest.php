<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Result\ContextualQueryRunner;
use Netgen\Layouts\Collection\Result\Slot;
use PHPUnit\Framework\TestCase;

final class ContextualQueryRunnerTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Collection\Result\ContextualQueryRunner::count
     * @covers \Netgen\Layouts\Collection\Result\ContextualQueryRunner::runQuery
     */
    public function testRunner(): void
    {
        $queryRunner = new ContextualQueryRunner();

        $values = iterator_to_array($queryRunner->runQuery(new Query(), 0, 5));

        self::assertCount(5, $values);
        self::assertContainsOnlyInstancesOf(Slot::class, $values);
        self::assertSame(intdiv(PHP_INT_MAX - 1, 2), $queryRunner->count(new Query()));
    }
}
