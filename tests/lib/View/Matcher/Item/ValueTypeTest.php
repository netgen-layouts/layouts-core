<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Item;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Item\ValueType;
use Netgen\Layouts\View\View\ItemView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValueType::class)]
final class ValueTypeTest extends TestCase
{
    private ValueType $matcher;

    protected function setUp(): void
    {
        $this->matcher = new ValueType();
    }

    /**
     * @param mixed[] $config
     */
    #[DataProvider('matchDataProvider')]
    public function testMatch(array $config, bool $expected): void
    {
        $view = new ItemView(CmsItem::fromArray(['valueType' => 'value']), 'view_type');

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function testMatchWithNullCmsItem(): void
    {
        $view = new ItemView(new NullCmsItem('value'), 'view_type');

        self::assertTrue($this->matcher->match($view, ['null']));
    }

    public function testMatchWithNullCmsItemReturnsFalse(): void
    {
        $view = new ItemView(new NullCmsItem('value'), 'view_type');

        self::assertFalse($this->matcher->match($view, ['test']));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['other'], false],
            [['value'], true],
            [['other1', 'other2'], false],
            [['other', 'value'], true],
        ];
    }

    public function testMatchWithNoItemView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
