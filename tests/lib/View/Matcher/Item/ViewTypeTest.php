<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Item;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Item\ViewType;
use Netgen\BlockManager\View\View\ItemView;
use PHPUnit\Framework\TestCase;

final class ViewTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new ViewType();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Item\ViewType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $view = new ItemView(new CmsItem(), 'view_type');

        $this->assertSame($expected, $this->matcher->match($view, $config));
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
     * @covers \Netgen\BlockManager\View\Matcher\Item\ViewType::match
     */
    public function testMatchWithNoItemView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
