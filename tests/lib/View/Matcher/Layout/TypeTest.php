<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Layout;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Layout\Type\NullLayoutType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Layout\Type;
use Netgen\Layouts\View\View\LayoutTypeView;
use Netgen\Layouts\View\View\LayoutView;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    private Type $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Type();
    }

    /**
     * @param mixed[] $config
     *
     * @covers \Netgen\Layouts\View\Matcher\Layout\Type::match
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $layout = Layout::fromArray(
            [
                'layoutType' => LayoutType::fromArray(['identifier' => '4_zones_a']),
            ],
        );

        $view = new LayoutView($layout);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Layout\Type::match
     */
    public function testMatchWithNullLayoutType(): void
    {
        $layout = Layout::fromArray(
            [
                'layoutType' => new NullLayoutType('type'),
            ],
        );

        $view = new LayoutView($layout);

        self::assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Layout\Type::match
     */
    public function testMatchWithNullLayoutTypeReturnsFalse(): void
    {
        $layout = Layout::fromArray(
            [
                'layoutType' => new NullLayoutType('type'),
            ],
        );

        $view = new LayoutView($layout);

        self::assertFalse($this->matcher->match($view, ['test']));
    }

    /**
     * @param mixed[] $config
     *
     * @covers \Netgen\Layouts\View\Matcher\Layout\Type::match
     *
     * @dataProvider matchLayoutTypeDataProvider
     */
    public function testMatchLayoutType(array $config, bool $expected): void
    {
        $view = new LayoutTypeView(LayoutType::fromArray(['identifier' => '4_zones_a']));

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

    public static function matchLayoutTypeDataProvider(): iterable
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
     * @covers \Netgen\Layouts\View\Matcher\Layout\Type::match
     */
    public function testMatchWithNoLayoutOrLayoutTypeView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
