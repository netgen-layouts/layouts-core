<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\View\Matcher\Block\DefinitionIdentifier;
use Netgen\BlockManager\Tests\View\Stubs\View;

class DefinitionIdentifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new DefinitionIdentifier();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionIdentifier::match
     * @covers \Netgen\BlockManager\View\Matcher\Matcher::setConfig
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $this->matcher->setConfig($config);

        $block = new Block(
            array(
                'definitionIdentifier' => 'paragraph',
            )
        );

        $view = new BlockView();
        $view->setBlock($block);

        self::assertEquals($expected, $this->matcher->match($view));
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
            array(array('paragraph'), true),
            array(array('title', 'title_2'), false),
            array(array('title', 'paragraph'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionIdentifier::match
     */
    public function testMatchWithNoBlockView()
    {
        self::assertFalse($this->matcher->match(new View()));
    }
}
