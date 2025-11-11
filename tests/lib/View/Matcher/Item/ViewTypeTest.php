<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Item;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Item\ViewType;
use Netgen\Layouts\View\View\ItemView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ViewType::class)]
final class ViewTypeTest extends TestCase
{
    private ViewType $matcher;

    protected function setUp(): void
    {
        $this->matcher = new ViewType();
    }

    /**
     * @param mixed[] $config
     */
    #[DataProvider('matchDataProvider')]
    public function testMatch(array $config, bool $expected): void
    {
        $view = new ItemView(new CmsItem(), 'view_type');

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['other'], false],
            [['view_type'], true],
            [['other1', 'other2'], false],
            [['other', 'view_type'], true],
        ];
    }

    public function testMatchWithNoItemView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
