<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Zone;

use Netgen\BlockManager\API\Values\Block\BlockList;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Layout\ZoneList;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Zone\LayoutType as LayoutTypeMatcher;
use Netgen\BlockManager\View\View\ZoneView;
use Netgen\BlockManager\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\TestCase;

final class LayoutTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new LayoutTypeMatcher();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Zone\LayoutType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $layout = Layout::fromArray(
            [
                'layoutType' => LayoutType::fromArray(['identifier' => '4_zones_a']),
                'zones' => new ZoneList(['left' => Zone::fromArray(['identifier' => 'left'])]),
            ]
        );

        $view = new ZoneView(new ZoneReference($layout, 'left'), new BlockList());

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
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
     * @covers \Netgen\BlockManager\View\Matcher\Zone\LayoutType::match
     */
    public function testMatchWithNoZoneView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
