<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultItemBuilder;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\Item as CmsItem;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Item\UrlBuilderInterface;
use Netgen\BlockManager\Parameters\Parameter;
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
    private $urlBuilderMock;

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
        $this->urlBuilderMock = $this->createMock(UrlBuilderInterface::class);
        $this->visibilityResolverMock = $this->createMock(VisibilityResolverInterface::class);

        $this->resultItemBuilder = new ResultItemBuilder(
            $this->itemLoaderMock,
            $this->itemBuilderMock,
            $this->urlBuilderMock,
            $this->visibilityResolverMock
        );
    }

    /**
     * @param bool $itemVisible
     * @param bool $configVisible
     * @param bool $resolverVisible
     * @param bool $resultVisible
     *
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::isResultVisible
     *
     * @dataProvider buildProvider
     */
    public function testBuild($itemVisible, $configVisible, $resolverVisible, $resultVisible)
    {
        $collectionItem = new CollectionItem(
            array(
                'value' => 42,
                'valueType' => 'ezlocation',
                'configs' => array(
                    'visibility' => new Config(
                        array(
                            'parameters' => array(
                                'visibility_status' => new Parameter(
                                    array(
                                        'value' => $configVisible ? CollectionItem::VISIBILITY_VISIBLE : CollectionItem::VISIBILITY_HIDDEN,
                                    )
                                ),
                                'visible_from' => new Parameter(
                                    array(
                                        'value' => null,
                                    )
                                ),
                                'visible_to' => new Parameter(
                                    array(
                                        'value' => null,
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $item = new CmsItem(
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

        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($item))
            ->will($this->returnValue('/some/url'));

        $itemVisible && $configVisible ?
            $this->visibilityResolverMock
                ->expects($this->once())
                ->method('isVisible')
                ->with($this->equalTo($collectionItem))
                ->will($this->returnValue($resolverVisible)) :
            $this->visibilityResolverMock
                ->expects($this->never())
                ->method('isVisible');

        $resultItem = $this->resultItemBuilder->build($collectionItem, 42);

        $hiddenStatus = null;
        if ($itemVisible === false) {
            $hiddenStatus = Result::HIDDEN_BY_CMS;
        } elseif ($configVisible === false) {
            $hiddenStatus = Result::HIDDEN_BY_CONFIG;
        } elseif ($resolverVisible === false) {
            $hiddenStatus = Result::HIDDEN_BY_CODE;
        }

        $this->assertEquals(
            new Result(
                array(
                    'item' => $item,
                    'collectionItem' => $collectionItem,
                    'type' => Result::TYPE_MANUAL,
                    'url' => '/some/url',
                    'position' => 42,
                    'isVisible' => $resultVisible,
                    'hiddenStatus' => $hiddenStatus,
                )
            ),
            $resultItem
        );
    }

    public function buildProvider()
    {
        return array(
            array(true, true, true, true),
            array(true, true, false, false),
            array(false, true, true, false),
            array(false, true, false, false),
            array(true, false, true, false),
            array(true, false, false, false),
            array(false, false, true, false),
            array(false, false, false, false),
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::isResultVisible
     */
    public function testBuildWithCmsItem()
    {
        $item = new CmsItem(
            array(
                'value' => 100,
                'valueType' => 'dynamicValue',
                'isVisible' => true,
            )
        );

        $this->itemBuilderMock
            ->expects($this->never())
            ->method('build');

        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($item))
            ->will($this->returnValue('/some/url'));

        $resultItem = $this->resultItemBuilder->build($item, 42);

        $this->assertEquals(
            new Result(
                array(
                    'item' => $item,
                    'collectionItem' => null,
                    'type' => Result::TYPE_DYNAMIC,
                    'url' => '/some/url',
                    'position' => 42,
                    'isVisible' => true,
                    'hiddenStatus' => null,
                )
            ),
            $resultItem
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::isResultVisible
     */
    public function testBuildWithCmsValueObject()
    {
        $item = new CmsItem(
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

        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($item))
            ->will($this->returnValue('/some/url'));

        $resultItem = $this->resultItemBuilder->build(new stdClass(), 42);

        $this->assertEquals(
            new Result(
                array(
                    'item' => $item,
                    'collectionItem' => null,
                    'type' => Result::TYPE_DYNAMIC,
                    'url' => '/some/url',
                    'position' => 42,
                    'isVisible' => true,
                    'hiddenStatus' => null,
                )
            ),
            $resultItem
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::build
     * @covers \Netgen\BlockManager\Collection\Result\ResultItemBuilder::isResultVisible
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

        $this->urlBuilderMock
            ->expects($this->never())
            ->method('getUrl');

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
                    'hiddenStatus' => null,
                )
            ),
            $resultItem
        );
    }
}
