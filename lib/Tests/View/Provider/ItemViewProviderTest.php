<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\View\Provider\ItemViewProvider;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View\ItemViewInterface;
use PHPUnit\Framework\TestCase;

class ItemViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $itemViewProvider;

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
        $view = $this->itemViewProvider->provideView($item, array('viewType' => 'view_type'));

        self::assertInstanceOf(ItemViewInterface::class, $view);

        self::assertEquals($item, $view->getItem());
        self::assertNull($view->getTemplate());
        self::assertEquals(
            array(
                'item' => $item,
                'viewType' => 'view_type',
            ),
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::provideView
     * @expectedException \RuntimeException
     */
    public function testProvideViewThrowsRuntimeExceptionOnMissingViewType()
    {
        $this->itemViewProvider->provideView(new Item());
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::provideView
     * @expectedException \RuntimeException
     */
    public function testProvideViewThrowsRuntimeExceptionOnInvalidViewType()
    {
        $this->itemViewProvider->provideView(new Item(), array('viewType' => 42));
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
        self::assertEquals($supports, $this->itemViewProvider->supports($value));
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
