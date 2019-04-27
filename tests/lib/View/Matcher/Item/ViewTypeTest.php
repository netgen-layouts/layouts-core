<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Item;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Item\ViewType;
use Netgen\Layouts\View\View\ItemView;
use PHPUnit\Framework\TestCase;

final class ViewTypeTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\View\Matcher\MatcherInterface
     */
    private $matcher;

    protected function setUp(): void
    {
        $this->matcher = new ViewType();
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Item\ViewType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $view = new ItemView(new CmsItem(), 'view_type');

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['other'], false],
            [['view_type'], true],
            [['other1', 'other2'], false],
            [['other', 'view_type'], true],
        ];
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Item\ViewType::match
     */
    public function testMatchWithNoItemView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
