<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Block\Locale;
use Netgen\BlockManager\View\View\BlockView;
use PHPUnit\Framework\TestCase;

final class LocaleTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new Locale();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\Locale::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $block = new Block(
            [
                'locale' => 'en',
            ]
        );

        $view = new BlockView($block);

        $this->assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['fr'], false],
            [['en'], true],
            [['fr', 'de'], false],
            [['fr', 'en'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\Locale::match
     */
    public function testMatchWithNoBlockView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
