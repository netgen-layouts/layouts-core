<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Block\Definition;
use Netgen\Layouts\View\Matcher\Block\DefinitionTrait;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Definition::class)]
#[CoversTrait(DefinitionTrait::class)]
final class DefinitionTest extends TestCase
{
    private Definition $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Definition();
    }

    /**
     * @param mixed[] $config
     */
    #[DataProvider('matchDataProvider')]
    public function testMatch(array $config, bool $expected): void
    {
        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(['identifier' => 'text']),
            ],
        );

        $view = new BlockView($block);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function testMatchWithNullDefinition(): void
    {
        $block = Block::fromArray(
            [
                'definition' => new NullBlockDefinition('definition'),
            ],
        );

        $view = new BlockView($block);

        self::assertTrue($this->matcher->match($view, ['null']));
    }

    public function testMatchWithNullDefinitionReturnsFalse(): void
    {
        $block = Block::fromArray(
            [
                'definition' => new NullBlockDefinition('definition'),
            ],
        );

        $view = new BlockView($block);

        self::assertFalse($this->matcher->match($view, ['test']));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['title'], false],
            [['text'], true],
            [['title', 'title_2'], false],
            [['title', 'text'], true],
        ];
    }

    public function testMatchWithNoBlockView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
