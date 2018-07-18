<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\NullBlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Block\Definition;
use Netgen\BlockManager\View\View\BlockView;
use PHPUnit\Framework\TestCase;

final class DefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new Definition();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\Definition::match
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(['identifier' => 'text']),
            ]
        );

        $view = new BlockView($block);

        $this->assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\Definition::match
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     */
    public function testMatchWithNullDefinition(): void
    {
        $block = Block::fromArray(
            [
                'definition' => new NullBlockDefinition('definition'),
            ]
        );

        $view = new BlockView($block);

        $this->assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\Definition::match
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     */
    public function testMatchWithNullDefinitionReturnsFalse(): void
    {
        $block = Block::fromArray(
            [
                'definition' => new NullBlockDefinition('definition'),
            ]
        );

        $view = new BlockView($block);

        $this->assertFalse($this->matcher->match($view, ['test']));
    }

    public function matchProvider(): array
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
     * @covers \Netgen\BlockManager\View\Matcher\Block\Definition::match
     */
    public function testMatchWithNoBlockView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
