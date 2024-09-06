<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\ZoneViewProvider;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use Netgen\Layouts\View\View\ZoneViewInterface;
use PHPUnit\Framework\TestCase;

final class ZoneViewProviderTest extends TestCase
{
    private ZoneViewProvider $ZoneViewProvider;

    protected function setUp(): void
    {
        $this->ZoneViewProvider = new ZoneViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\ZoneViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $zone = Zone::fromArray(['identifier' => 'zone']);
        $layout = Layout::fromArray(
            [
                'zones' => new ArrayCollection(
                    [
                        'zone' => $zone,
                    ],
                ),
            ],
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
            $view->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\ZoneViewProvider::provideView
     */
    public function testProvideViewThrowsViewProviderExceptionOnMissingBlocks(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the zone view, "blocks" parameter needs to be provided.');

        $this->ZoneViewProvider->provideView(new ZoneReference(new Layout(), 'zone'));
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\ZoneViewProvider::provideView
     */
    public function testProvideViewThrowsViewProviderExceptionOnInvalidBlocks(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the zone view, "blocks" parameter needs to be of "Netgen\Layouts\API\Values\Block\BlockList" type.');

        $this->ZoneViewProvider->provideView(new ZoneReference(new Layout(), 'zone'), ['blocks' => 42]);
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\View\Provider\ZoneViewProvider::supports
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->ZoneViewProvider->supports($value));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [new Value(), false],
            [new Zone(), false],
            [new ZoneReference(new Layout(), 'zone'), true],
            [new Layout(), false],
        ];
    }
}
