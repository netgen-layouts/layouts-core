<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\View\Provider\BlockViewProvider;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\API\Stubs\Value;

class BlockViewProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     */
    public function testProvideView()
    {
        $block = new Block(array('id' => 42));

        $blockViewProvider = new BlockViewProvider();

        /** @var \Netgen\BlockManager\View\BlockViewInterface $view */
        $view = $blockViewProvider->provideView($block);

        self::assertInstanceOf('Netgen\BlockManager\View\BlockViewInterface', $view);

        self::assertEquals($block, $view->getBlock());
        self::assertEquals(null, $view->getTemplate());
        self::assertEquals(
            array(
                'block' => $block,
            ),
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $blockViewProvider = new BlockViewProvider();
        self::assertEquals($supports, $blockViewProvider->supports($value));
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
            array(new Block(), true),
            array(new Layout(), false),
        );
    }
}
