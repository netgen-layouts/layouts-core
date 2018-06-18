<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ContextualQueryRunner;
use Netgen\BlockManager\Collection\Result\Slot;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

final class ContextualQueryRunnerTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\ContextualQueryRunner::count
     * @covers \Netgen\BlockManager\Collection\Result\ContextualQueryRunner::runQuery
     */
    public function testRunner(): void
    {
        $queryRunner = new ContextualQueryRunner();

        $count = 0;
        foreach ($queryRunner->runQuery(new Query(), 0, 5) as $value) {
            $this->assertInstanceOf(Slot::class, $value);
            ++$count;
        }

        $this->assertSame(5, $count);

        $this->assertSame((int) ((PHP_INT_MAX - 1) / 2), $queryRunner->count(new Query()));
    }
}
