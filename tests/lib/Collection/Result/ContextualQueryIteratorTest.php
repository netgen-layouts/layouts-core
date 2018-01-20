<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ContextualQueryIterator;
use Netgen\BlockManager\Collection\Result\Slot;
use PHPUnit\Framework\TestCase;

final class ContextualQueryIteratorTest extends TestCase
{
    use IteratorTestTrait;

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ContextualQueryIterator::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ContextualQueryIterator::buildIterator
     * @covers \Netgen\BlockManager\Collection\Result\ContextualQueryIterator::generateSlots
     * @covers \Netgen\BlockManager\Collection\Result\ContextualQueryIterator::count
     */
    public function testIterator()
    {
        $queryIterator = new ContextualQueryIterator(5);

        $values = array();
        foreach ($queryIterator as $value) {
            $values[] = $value;
        }

        $this->assertEquals(array(new Slot(), new Slot(), new Slot(), new Slot(), new Slot()), $values);
        $this->assertEquals(5, $queryIterator->count());
    }
}
