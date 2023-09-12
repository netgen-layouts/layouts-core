<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Block\Definition;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\TestCase;

final class DefinitionTest extends TestCase
{
    private Definition $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Definition();
    }

    /**
     * @param mixed[] $config
     *
     * @covers \Netgen\Layouts\View\Matcher\Block\Definition::match
     * @covers \Netgen\Layouts\View\Matcher\Block\DefinitionTrait::doMatch
     *
     * @dataProvider matchDataProvider
     */
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

    /**
     * @covers \Netgen\Layouts\View\Matcher\Block\Definition::match
     * @covers \Netgen\Layouts\View\Matcher\Block\DefinitionTrait::doMatch
     */
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

    /**
     * @covers \Netgen\Layouts\View\Matcher\Block\Definition::match
     * @covers \Netgen\Layouts\View\Matcher\Block\DefinitionTrait::doMatch
     */
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

    /**
     * @covers \Netgen\Layouts\View\Matcher\Block\Definition::match
     */
    public function testMatchWithNoBlockView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
