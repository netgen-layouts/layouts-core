<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Block\Locale;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Locale::class)]
final class LocaleTest extends TestCase
{
    private Locale $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Locale();
    }

    /**
     * @param mixed[] $config
     */
    #[DataProvider('matchDataProvider')]
    public function testMatch(array $config, bool $expected): void
    {
        $block = Block::fromArray(
            [
                'locale' => 'en',
            ],
        );

        $view = new BlockView($block);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['fr'], false],
            [['en'], true],
            [['fr', 'de'], false],
            [['fr', 'en'], true],
        ];
    }

    public function testMatchWithNoBlockView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
