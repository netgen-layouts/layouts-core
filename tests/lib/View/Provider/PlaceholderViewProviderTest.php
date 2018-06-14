<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\PlaceholderViewProvider;
use Netgen\BlockManager\View\View\PlaceholderViewInterface;
use PHPUnit\Framework\TestCase;

final class PlaceholderViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $placeholderViewProvider;

    public function setUp(): void
    {
        $this->placeholderViewProvider = new PlaceholderViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $placeholder = new Placeholder();

        /** @var \Netgen\BlockManager\View\View\PlaceholderViewInterface $view */
        $view = $this->placeholderViewProvider->provideView(
            $placeholder,
            [
                'block' => new Block(),
            ]
        );

        $this->assertInstanceOf(PlaceholderViewInterface::class, $view);

        $this->assertEquals(new Placeholder(), $view->getPlaceholder());
        $this->assertEquals(new Block(), $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            [
                'placeholder' => new Placeholder(),
                'block' => new Block(),
            ],
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage To build the placeholder view, "block" parameter needs to be provided.
     */
    public function testProvideViewThrowsRuntimeExceptionOnMissingBlock(): void
    {
        $this->placeholderViewProvider->provideView(new Placeholder());
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage To build the placeholder view, "block" parameter needs to be of "Netgen\BlockManager\API\Values\Block\Block" type.
     */
    public function testProvideViewThrowsRuntimeExceptionOnInvalidBlock(): void
    {
        $this->placeholderViewProvider->provideView(new Placeholder(), ['block' => 42]);
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        $this->assertEquals($supports, $this->placeholderViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [new Placeholder(), true],
            [new Block(), false],
        ];
    }
}
