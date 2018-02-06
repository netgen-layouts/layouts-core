<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultItemBuilder;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ResultItemBuilderTest extends TestCase
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

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultItemBuilder
     */
    private $resultItemBuilder;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);
        $this->visibilityResolverMock = $this->createMock(VisibilityResolverInterface::class);

        $this->resultItemBuilder = new ResultItemBuilder(
            $this->itemLoaderMock,
            $this->itemBuilderMock,
            $this->visibilityResolverMock
        );
    }

    /**
     * @param bool $itemVisible
     * @param bool $providerVisible
     * @param bool $resultVisible
     *
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     *
     * @dataProvider buildProvider
     */
    public function testBuild($itemVisible, $providerVisible, $resultVisible)
    {
        $collectionItem = new CollectionItem(
            array(
                'value' => 42,
                'valueType' => 'ezlocation',
            )
        );

        $item = new Item(
            array(
                'value' => 42,
                'valueType' => 'ezlocation',
                'isVisible' => $itemVisible,
            )
        );

        $this->itemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('ezlocation'))
            ->will($this->returnValue($item));

        $itemVisible ?
            $this->visibilityResolverMock
                ->expects($this->once())
                ->method('isVisible')
                ->with($this->equalTo($collectionItem))
                ->will($this->returnValue($providerVisible)) :
            $this->visibilityResolverMock
                ->expects($this->never())
                ->method('isVisible');

        $resultItem = $this->resultItemBuilder->build($collectionItem, 42);

        $this->assertEquals(
            new Result(
                array(
                    'item' => $item,
                    'collectionItem' => $collectionItem,
                    'type' => Result::TYPE_MANUAL,
                    'position' => 42,
                    'isVisible' => $resultVisible,
                )
            ),
            $resultItem
        );
    }

    public function buildProvider()
    {
        return array(
            array(true, true, true),
            array(true, false, false),
            array(false, true, false),
            array(false, false, false),
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     */
    public function testBuildWithCmsItem()
    {
        $item = new Item(
            array(
                'value' => 100,
                'valueType' => 'dynamicValue',
                'isVisible' => true,
            )
        );

        $this->itemBuilderMock
            ->expects($this->never())
            ->method('build');

        $resultItem = $this->resultItemBuilder->build($item, 42);

        $this->assertEquals(
            new Result(
                array(
                    'item' => $item,
                    'collectionItem' => null,
                    'type' => Result::TYPE_DYNAMIC,
                    'position' => 42,
                    'isVisible' => true,
                )
            ),
            $resultItem
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     */
    public function testBuildWithCmsValueObject()
    {
        $item = new Item(
            array(
                'value' => 100,
                'valueType' => 'dynamicValue',
                'isVisible' => true,
            )
        );

        $this->itemBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($this->equalTo(new stdClass()))
            ->will($this->returnValue($item));

        $resultItem = $this->resultItemBuilder->build(new stdClass(), 42);

        $this->assertEquals(
            new Result(
                array(
                    'item' => $item,
                    'collectionItem' => null,
                    'type' => Result::TYPE_DYNAMIC,
                    'position' => 42,
                    'isVisible' => true,
                )
            ),
            $resultItem
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     */
    public function testBuildWithInvalidCollectionItem()
    {
        $collectionItem = new CollectionItem(
            array(
                'value' => 999,
                'valueType' => 'ezlocation',
            )
        );

        $this->itemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(999), $this->equalTo('ezlocation'))
            ->will($this->throwException(new ItemException()));

        $resultItem = $this->resultItemBuilder->build($collectionItem, 999);

        $this->assertEquals(
            new Result(
                array(
                    'item' => new NullItem(
                        array(
                            'value' => 999,
                        )
                    ),
                    'collectionItem' => $collectionItem,
                    'type' => Result::TYPE_MANUAL,
                    'position' => 999,
                    'isVisible' => true,
                )
            ),
            $resultItem
        );
    }
}
