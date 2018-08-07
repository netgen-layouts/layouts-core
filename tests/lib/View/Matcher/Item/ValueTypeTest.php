<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Item;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\NullCmsItem;
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
        $view = new ItemView(CmsItem::fromArray(['valueType' => 'value']), 'view_type');

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Item\ValueType::match
     */
    public function testMatchWithNullCmsItem(): void
    {
        $view = new ItemView(new NullCmsItem('value'), 'view_type');

        self::assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Item\ValueType::match
     */
    public function testMatchWithNullCmsItemReturnsFalse(): void
    {
        $view = new ItemView(new NullCmsItem('value'), 'view_type');

        self::assertFalse($this->matcher->match($view, ['test']));
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
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
