<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\ItemViewProvider;
use Netgen\BlockManager\View\View\ItemViewInterface;
use PHPUnit\Framework\TestCase;

class ItemViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $itemViewProvider;

    public function setUp()
    {
        $this->itemViewProvider = new ItemViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::provideView
     */
    public function testProvideView()
    {
        $item = new Item();

        /** @var \Netgen\BlockManager\View\View\ItemViewInterface $view */
        $view = $this->itemViewProvider->provideView($item, array('view_type' => 'view_type'));

        $this->assertInstanceOf(ItemViewInterface::class, $view);

        $this->assertEquals($item, $view->getItem());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'item' => $item,
                'view_type' => 'view_type',
            ),
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage To build the item view, "view_type" parameter needs to be provided.
     */
    public function testProvideViewThrowsViewProviderExceptionOnMissingViewType()
    {
        $this->itemViewProvider->provideView(new Item());
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage To build the item view, "view_type" parameter needs to be of "string" type.
     */
    public function testProvideViewThrowsViewProviderExceptionOnInvalidViewType()
    {
        $this->itemViewProvider->provideView(new Item(), array('view_type' => 42));
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $this->assertEquals($supports, $this->itemViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Value(), false),
            array(new Item(), true),
            array(new Layout(), false),
        );
    }
}
