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

        $values = [];
        foreach ($queryRunner->runQuery(new Query(), 0, 5) as $value) {
            $values[] = $value;
        }

        $this->assertEquals([new Slot(), new Slot(), new Slot(), new Slot(), new Slot()], $values);
        $this->assertEquals((PHP_INT_MAX - 1) / 2, $queryRunner->count(new Query()));
    }
}
