<?php

namespace Netgen\BlockManager\View\Tests\Matcher\Block;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\View\Matcher\Block\DefinitionIdentifier;
use Netgen\BlockManager\View\Tests\Stubs\View;
use PHPUnit_Framework_TestCase;

class DefinitionIdentifierTest extends PHPUnit_Framework_TestCase
{
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
        $matcher = new DefinitionIdentifier();
        $matcher->setConfig($config);

        $block = new Block(
            array(
                'definitionIdentifier' => 'paragraph'
            )
        );

        $view = new BlockView();
        $view->setBlock($block);

        self::assertEquals($expected, $matcher->match($view));
    }

    /**
     * Provider for {@link self::testMatch}
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
            array(array('title', 'paragraph'), true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionIdentifier::match
     */
    public function testMatchWithNoBlockView()
    {
        $matcher = new DefinitionIdentifier();
        self::assertEquals(false, $matcher->match(new View()));
    }
}
