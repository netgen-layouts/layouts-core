<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Block\BlockList;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\View\Provider\ZoneViewProvider;
use Netgen\BlockManager\View\View\ZoneView\ZoneReference;
use Netgen\BlockManager\View\View\ZoneViewInterface;
use PHPUnit\Framework\TestCase;

final class ZoneViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $ZoneViewProvider;

    public function setUp(): void
    {
        $this->ZoneViewProvider = new ZoneViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ZoneViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $zone = Zone::fromArray(['identifier' => 'zone']);
        $layout = Layout::fromArray(
            [
                'zones' => new ArrayCollection(
                    [
                        'zone' => $zone,
                    ]
                ),
            ]
        );

        $blocks = new BlockList();

        $view = $this->ZoneViewProvider->provideView(new ZoneReference($layout, 'zone'), ['blocks' => $blocks]);

        self::assertInstanceOf(ZoneViewInterface::class, $view);

        self::assertSame($layout, $view->getLayout());
        self::assertSame($zone, $view->getZone());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'layout' => $layout,
                'zone' => $zone,
                'blocks' => $blocks,
            ],
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ZoneViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage To build the zone view, "blocks" parameter needs to be provided.
     */
    public function testProvideViewThrowsViewProviderExceptionOnMissingBlocks(): void
    {
        $this->ZoneViewProvider->provideView(new ZoneReference(new Layout(), 'zone'));
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ZoneViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage To build the zone view, "blocks" parameter needs to be of "Netgen\BlockManager\API\Values\Block\BlockList" type.
     */
    public function testProvideViewThrowsViewProviderExceptionOnInvalidBlocks(): void
    {
        $this->ZoneViewProvider->provideView(new ZoneReference(new Layout(), 'zone'), ['blocks' => 42]);
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\ZoneViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->ZoneViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [new Zone(), false],
            [new ZoneReference(new Layout(), 'zone'), true],
            [new Layout(), false],
        ];
    }
}
