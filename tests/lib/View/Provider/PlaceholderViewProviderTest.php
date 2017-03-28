<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\PlaceholderViewProvider;
use Netgen\BlockManager\View\View\PlaceholderViewInterface;
use PHPUnit\Framework\TestCase;

class PlaceholderViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $placeholderViewProvider;

    public function setUp()
    {
        $this->placeholderViewProvider = new PlaceholderViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::provideView
     */
    public function testProvideView()
    {
        $placeholder = new Placeholder();

        /** @var \Netgen\BlockManager\View\View\PlaceholderViewInterface $view */
        $view = $this->placeholderViewProvider->provideView(
            $placeholder,
            array(
                'block' => new Block(),
            )
        );

        $this->assertInstanceOf(PlaceholderViewInterface::class, $view);

        $this->assertEquals(new Placeholder(), $view->getPlaceholder());
        $this->assertEquals(new Block(), $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'placeholder' => new Placeholder(),
                'block' => new Block(),
            ),
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage To build the placeholder view, "block" parameter needs to be provided.
     */
    public function testProvideViewThrowsRuntimeExceptionOnMissingBlock()
    {
        $this->placeholderViewProvider->provideView(new Placeholder());
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage To build the placeholder view, "block" parameter needs to be of "Netgen\BlockManager\API\Values\Block\Block" type.
     */
    public function testProvideViewThrowsRuntimeExceptionOnInvalidBlock()
    {
        $this->placeholderViewProvider->provideView(new Placeholder(), array('block' => 42));
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $this->assertEquals($supports, $this->placeholderViewProvider->supports($value));
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
            array(new Placeholder(), true),
            array(new Block(), false),
        );
    }
}
