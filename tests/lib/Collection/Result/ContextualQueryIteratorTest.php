<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ContextualQueryIterator;
use Netgen\BlockManager\Collection\Result\Slot;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class ContextualQueryIteratorTest extends TestCase
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
        $queryItems = array(40, 41, 42, 43, 44, 45, 46, 47, 48);

        $queryType = new QueryType('query', $queryItems);
        $query = new Query(array('queryType' => $queryType));

        $queryIterator = new ContextualQueryIterator($query, 0, 5);

        $values = array();
        foreach ($queryIterator as $value) {
            $values[] = $value;
        }

        $this->assertEquals(array(new Slot(), new Slot(), new Slot(), new Slot(), new Slot()), $values);
        $this->assertEquals(5, $queryIterator->count());
    }
}
