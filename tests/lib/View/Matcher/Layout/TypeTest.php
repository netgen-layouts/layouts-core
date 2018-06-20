<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Layout;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\NullLayoutType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Layout\Type;
use Netgen\BlockManager\View\View\LayoutTypeView;
use Netgen\BlockManager\View\View\LayoutView;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new Type();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $layout = new Layout(
            [
                'layoutType' => new LayoutType(['identifier' => '4_zones_a']),
            ]
        );

        $view = new LayoutView($layout);

        $this->assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     */
    public function testMatchWithNullLayoutType(): void
    {
        $layout = new Layout(
            [
                'layoutType' => new NullLayoutType('type'),
            ]
        );

        $view = new LayoutView($layout);

        $this->assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     */
    public function testMatchWithNullLayoutTypeReturnsFalse(): void
    {
        $layout = new Layout(
            [
                'layoutType' => new NullLayoutType('type'),
            ]
        );

        $view = new LayoutView($layout);

        $this->assertFalse($this->matcher->match($view, ['test']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     * @dataProvider matchLayoutTypeProvider
     */
    public function testMatchLayoutType(array $config, bool $expected): void
    {
        $view = new LayoutTypeView(new LayoutType(['identifier' => '4_zones_a']));

        $this->assertSame($expected, $this->matcher->match($view, $config));
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

    public function matchLayoutTypeProvider(): array
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
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     */
    public function testMatchWithNoLayoutOrLayoutTypeView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
