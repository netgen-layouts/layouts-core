<?php

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

    public function setUp()
    {
        $this->matcher = new Definition();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Block\Definition::match
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $block = new Block(
            [
                'definition' => new BlockDefinition(['identifier' => 'text']),
            ]
        );

        $view = new BlockView(
            [
                'block' => $block,
            ]
        );

        $this->assertEquals($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\Definition::match
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     */
    public function testMatchWithNullDefinition()
    {
        $block = new Block(
            [
                'definition' => new NullBlockDefinition('definition'),
            ]
        );

        $view = new BlockView(
            [
                'block' => $block,
            ]
        );

        $this->assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\Definition::match
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     */
    public function testMatchWithNullDefinitionReturnsFalse()
    {
        $block = new Block(
            [
                'definition' => new NullBlockDefinition('definition'),
            ]
        );

        $view = new BlockView(
            [
                'block' => $block,
            ]
        );

        $this->assertFalse($this->matcher->match($view, ['test']));
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchProvider()
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
    public function testMatchWithNoBlockView()
    {
        $this->assertFalse($this->matcher->match(new View(['value' => new Value()]), []));
    }
}
