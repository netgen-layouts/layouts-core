<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Zone;

use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Zone\LayoutType as LayoutTypeMatcher;
use Netgen\Layouts\View\View\ZoneView;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\TestCase;

final class LayoutTypeTest extends TestCase
{
    private LayoutTypeMatcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new LayoutTypeMatcher();
    }

    /**
     * @param mixed[] $config
     *
     * @covers \Netgen\Layouts\View\Matcher\Zone\LayoutType::match
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $layout = Layout::fromArray(
            [
                'layoutType' => LayoutType::fromArray(['identifier' => '4_zones_a']),
                'zones' => new ZoneList(['left' => Zone::fromArray(['identifier' => 'left'])]),
            ],
        );

        $view = new ZoneView(new ZoneReference($layout, 'left'), new BlockList());

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['some_type'], false],
            [['4_zones_a'], true],
            [['some_type', 'some_type_2'], false],
            [['some_type', '4_zones_a'], true],
        ];
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Zone\LayoutType::match
     */
    public function testMatchWithNoZoneView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
