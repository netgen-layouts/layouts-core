<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\View\Provider\BlockViewProvider;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\BlockViewInterface;
use PHPUnit\Framework\TestCase;

class BlockViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $blockViewProvider;

    public function setUp()
    {
        $this->blockViewProvider = new BlockViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     */
    public function testProvideView()
    {
        $block = new Block(array('id' => 42));

        /** @var \Netgen\BlockManager\View\BlockViewInterface $view */
        $view = $this->blockViewProvider->provideView($block);

        self::assertInstanceOf(BlockViewInterface::class, $view);

        self::assertEquals($block, $view->getBlock());
        self::assertNull($view->getTemplate());
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
        self::assertEquals($supports, $this->blockViewProvider->supports($value));
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
