<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultItemBuilder;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\Item as CmsItem;
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
     * @var \Netgen\BlockManager\Collection\Result\ResultItemBuilder
     */
    private $resultItemBuilder;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);

        $this->resultItemBuilder = new ResultItemBuilder(
            $this->itemLoaderMock,
            $this->itemBuilderMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     */
    public function testBuild()
    {
        $collectionItem = new CollectionItem(
            array(
                'value' => 42,
                'valueType' => 'ezlocation',
            )
        );

        $item = new CmsItem(
            array(
                'value' => 42,
                'valueType' => 'ezlocation',
            )
        );

        $this->itemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('ezlocation'))
            ->will($this->returnValue($item));

        $resultItem = $this->resultItemBuilder->build($collectionItem, 42);

        $this->assertEquals(
            new Result(
                array(
                    'item' => $item,
                    'collectionItem' => $collectionItem,
                    'type' => Result::TYPE_MANUAL,
                    'position' => 42,
                )
            ),
            $resultItem
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     */
    public function testBuildWithCmsItem()
    {
        $item = new CmsItem(
            array(
                'value' => 100,
                'valueType' => 'dynamicValue',
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
        $item = new CmsItem(
            array(
                'value' => 100,
                'valueType' => 'dynamicValue',
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
                )
            ),
            $resultItem
        );
    }
}
