<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultFilterIterator;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\NullItem;
use PHPUnit\Framework\TestCase;

class ResultFilterIteratorTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultFilterIterator::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultFilterIterator::accept
     */
    public function testAccept()
    {
        $results = $this->getResults();

        $iterator = new ResultFilterIterator(new ArrayIterator($results));

        $this->assertEquals(
            array(
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'isVisible' => true,
                            )
                        ),
                    )
                ),
            ),
            iterator_to_array($iterator)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultFilterIterator::accept
     */
    public function testAcceptInvisibleItems()
    {
        $results = $this->getResults();

        $iterator = new ResultFilterIterator(
            new ArrayIterator($results),
            ResultSet::INCLUDE_INVISIBLE_ITEMS
        );

        $this->assertEquals(
            array(
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'isVisible' => true,
                            )
                        ),
                    )
                ),
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'isVisible' => false,
                            )
                        ),
                    )
                ),
            ),
            array_values(iterator_to_array($iterator))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultFilterIterator::accept
     */
    public function testAcceptInvalidItems()
    {
        $results = $this->getResults();

        $iterator = new ResultFilterIterator(
            new ArrayIterator($results),
            ResultSet::INCLUDE_INVALID_ITEMS
        );

        $this->assertEquals(
            array(
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'isVisible' => true,
                            )
                        ),
                    )
                ),
                new Result(
                    array(
                        'item' => new NullItem(),
                    )
                ),
            ),
            array_values(iterator_to_array($iterator))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultFilterIterator::accept
     */
    public function testAcceptAllItems()
    {
        $results = $this->getResults();

        $iterator = new ResultFilterIterator(
            new ArrayIterator($results),
            2 ** 32 - 1 // All flags active
        );

        $this->assertEquals($results, iterator_to_array($iterator));
    }

    /**
     * @return \Netgen\BlockManager\Collection\Result\Result[]
     */
    protected function getResults()
    {
        return array(
            new Result(
                array(
                    'item' => new Item(
                        array(
                            'isVisible' => true,
                        )
                    ),
                )
            ),
            new Result(
                array(
                    'item' => new Item(
                        array(
                            'isVisible' => false,
                        )
                    ),
                )
            ),
            new Result(
                array(
                    'item' => new NullItem(),
                )
            ),
        );
    }
}
