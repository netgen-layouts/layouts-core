<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Item\ValueType;
use Netgen\BlockManager\View\View\ItemView;
use PHPUnit\Framework\TestCase;

final class ValueTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new ValueType();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Item\ValueType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $view = new ItemView(new Item(['valueType' => 'value']), 'view_type');

        $this->assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Item\ValueType::match
     */
    public function testMatchWithNullItem(): void
    {
        $view = new ItemView(new NullItem('value'), 'view_type');

        $this->assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Item\ValueType::match
     */
    public function testMatchWithNullItemReturnsFalse(): void
    {
        $view = new ItemView(new NullItem('value'), 'view_type');

        $this->assertFalse($this->matcher->match($view, ['test']));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['other'], false],
            [['value'], true],
            [['other1', 'other2'], false],
            [['other', 'value'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Item\ValueType::match
     */
    public function testMatchWithNoItemView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
