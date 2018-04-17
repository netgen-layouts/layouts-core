<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ContextualQueryRunner;
use Netgen\BlockManager\Collection\Result\Slot;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

final class ContextualQueryRunnerTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\ContextualQueryRunner::__invoke
     * @covers \Netgen\BlockManager\Collection\Result\ContextualQueryRunner::count
     */
    public function testRunner()
    {
        $queryRunner = new ContextualQueryRunner(12);

        $values = array();
        foreach ($queryRunner(new Query(), 0, 5) as $value) {
            $values[] = $value;
        }

        $this->assertEquals(array(new Slot(), new Slot(), new Slot(), new Slot(), new Slot()), $values);
        $this->assertEquals(12, $queryRunner->count(new Query()));
    }
}
