<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultBuilderIterator;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ResultBuilderIteratorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $visibilityResolverMock;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);
        $this->visibilityResolverMock = $this->createMock(VisibilityResolverInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilderIterator::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilderIterator::current
     */
    public function testCurrent()
    {
        $items = $this->getItems();

        $this->itemBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($this->equalTo(new stdClass()))
            ->will(
                $this->returnValue(
                    new Item(
                        array(
                            'value' => 100,
                            'valueType' => 'dynamicValue',
                            'isVisible' => true,
                        )
                    )
                )
            );

        $this->itemLoaderMock
            ->expects($this->at(0))
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will(
                $this->returnValue(
                    new Item(
                        array(
                            'value' => 42,
                            'valueType' => 'value',
                            'isVisible' => true,
                        )
                    )
                )
            );

        $this->itemLoaderMock
            ->expects($this->at(1))
            ->method('load')
            ->with($this->equalTo(43), $this->equalTo('value'))
            ->will(
                $this->returnValue(
                    new Item(
                        array(
                            'value' => 43,
                            'valueType' => 'value',
                            'isVisible' => true,
                        )
                    )
                )
            );

        $this->itemLoaderMock
            ->expects($this->at(2))
            ->method('load')
            ->with($this->equalTo(44), $this->equalTo('value'))
            ->will(
                $this->returnValue(
                    new Item(
                        array(
                            'value' => 44,
                            'valueType' => 'value',
                            'isVisible' => false,
                        )
                    )
                )
            );

        $this->itemLoaderMock
            ->expects($this->at(3))
            ->method('load')
            ->with($this->equalTo(45), $this->equalTo('value'))
            ->will(
                $this->returnValue(
                    new Item(
                        array(
                            'value' => 45,
                            'valueType' => 'value',
                            'isVisible' => false,
                        )
                    )
                )
            );

        $this->itemLoaderMock
            ->expects($this->at(4))
            ->method('load')
            ->with($this->equalTo(999), $this->equalTo('value'))
            ->will($this->throwException(new ItemException()));

        $this->visibilityResolverMock
            ->expects($this->at(0))
            ->method('isVisible')
            ->with($this->equalTo($items[1]))
            ->will($this->returnValue(true));

        $this->visibilityResolverMock
            ->expects($this->at(1))
            ->method('isVisible')
            ->with($this->equalTo($items[2]))
            ->will($this->returnValue(false));

        $iterator = new ResultBuilderIterator(
            new ArrayIterator($items),
            $this->itemLoaderMock,
            $this->itemBuilderMock,
            $this->visibilityResolverMock
        );

        $this->assertEquals(
            array(
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'value' => 100,
                                'valueType' => 'dynamicValue',
                                'isVisible' => true,
                            )
                        ),
                        'collectionItem' => null,
                        'type' => Result::TYPE_DYNAMIC,
                        'position' => 0,
                    )
                ),
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'value' => 42,
                                'valueType' => 'value',
                                'isVisible' => true,
                            )
                        ),
                        'collectionItem' => new CollectionItem(
                            array(
                                'value' => 42,
                                'valueType' => 'value',
                            )
                        ),
                        'type' => Result::TYPE_MANUAL,
                        'position' => 1,
                        'isVisible' => true,
                    )
                ),
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'value' => 43,
                                'valueType' => 'value',
                                'isVisible' => true,
                            )
                        ),
                        'collectionItem' => new CollectionItem(
                            array(
                                'value' => 43,
                                'valueType' => 'value',
                            )
                        ),
                        'type' => Result::TYPE_MANUAL,
                        'position' => 2,
                        'isVisible' => false,
                    )
                ),
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'value' => 44,
                                'valueType' => 'value',
                                'isVisible' => false,
                            )
                        ),
                        'collectionItem' => new CollectionItem(
                            array(
                                'value' => 44,
                                'valueType' => 'value',
                            )
                        ),
                        'type' => Result::TYPE_MANUAL,
                        'position' => 3,
                        'isVisible' => false,
                    )
                ),
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'value' => 45,
                                'valueType' => 'value',
                                'isVisible' => false,
                            )
                        ),
                        'collectionItem' => new CollectionItem(
                            array(
                                'value' => 45,
                                'valueType' => 'value',
                            )
                        ),
                        'type' => Result::TYPE_MANUAL,
                        'position' => 4,
                        'isVisible' => false,
                    )
                ),
                new Result(
                    array(
                        'item' => new NullItem(
                            array(
                                'value' => 999,
                            )
                        ),
                        'collectionItem' => new CollectionItem(
                            array(
                                'value' => 999,
                                'valueType' => 'value',
                            )
                        ),
                        'type' => Result::TYPE_MANUAL,
                        'position' => 5,
                        'isVisible' => true,
                    )
                ),
            ),
            iterator_to_array($iterator)
        );
    }

    /**
     * @return mixed[]
     */
    private function getItems()
    {
        return array(
            new stdClass(),
            new CollectionItem(
                array(
                    'value' => 42,
                    'valueType' => 'value',
                )
            ),
            new CollectionItem(
                array(
                    'value' => 43,
                    'valueType' => 'value',
                )
            ),
            new CollectionItem(
                array(
                    'value' => 44,
                    'valueType' => 'value',
                )
            ),
            new CollectionItem(
                array(
                    'value' => 45,
                    'valueType' => 'value',
                )
            ),
            new CollectionItem(
                array(
                    'value' => 999,
                    'valueType' => 'value',
                )
            ),
        );
    }
}
