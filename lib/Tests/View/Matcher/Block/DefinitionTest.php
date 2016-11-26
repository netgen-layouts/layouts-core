<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\Matcher\Block\Definition;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new Definition();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Block\Definition::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $block = new Block(
            array(
                'blockDefinition' => new BlockDefinition('text'),
            )
        );

        $view = new BlockView(
            array(
                'valueObject' => $block,
            )
        );

        $this->assertEquals($expected, $this->matcher->match($view, $config));
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchProvider()
    {
        return array(
            array(array(), false),
            array(array('title'), false),
            array(array('text'), true),
            array(array('title', 'title_2'), false),
            array(array('title', 'text'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\Definition::match
     */
    public function testMatchWithNoBlockView()
    {
        $this->assertFalse($this->matcher->match(new View(array('valueObject' => new Value())), array()));
    }
}
