<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\ZoneViewProvider;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ZoneViewProvider::class)]
final class ZoneViewProviderTest extends TestCase
{
    private ZoneViewProvider $ZoneViewProvider;

    protected function setUp(): void
    {
        $this->ZoneViewProvider = new ZoneViewProvider();
    }

    public function testProvideView(): void
    {
        $zone = Zone::fromArray(['identifier' => 'zone']);
        $layout = Layout::fromArray(
            [
                'zones' => ZoneList::fromArray(
                    [
                        'zone' => $zone,
                    ],
                ),
            ],
        );

        $blocks = BlockList::fromArray([]);

        $view = $this->ZoneViewProvider->provideView(new ZoneReference($layout, 'zone'), ['blocks' => $blocks]);

        self::assertSame($layout, $view->layout);
        self::assertSame($zone, $view->zone);
        self::assertNull($view->template);
        self::assertSame(
            [
                'layout' => $layout,
                'zone' => $zone,
                'blocks' => $blocks,
            ],
            $view->parameters,
        );
    }

    public function testProvideViewThrowsViewProviderExceptionOnMissingBlocks(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the zone view, "blocks" parameter needs to be provided.');

        $this->ZoneViewProvider->provideView(new ZoneReference(new Layout(), 'zone'));
    }

    public function testProvideViewThrowsViewProviderExceptionOnInvalidBlocks(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the zone view, "blocks" parameter needs to be of "Netgen\Layouts\API\Values\Block\BlockList" type.');

        $this->ZoneViewProvider->provideView(new ZoneReference(new Layout(), 'zone'), ['blocks' => 42]);
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(object $value, bool $supports): void
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
