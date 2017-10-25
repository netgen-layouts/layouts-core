<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier;
use Netgen\BlockManager\View\View\BlockView;
use PHPUnit\Framework\TestCase;

class CollectionIdentifierTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new CollectionIdentifier();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $view = new BlockView(
            array(
                'block' => new Block(),
                'collection_identifier' => 'default',
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
            array(array('featured'), false),
            array(array('default'), true),
            array(array('featured', 'featured2'), false),
            array(array('featured', 'default'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier::match
     */
    public function testMatchWithNoBlockView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier::match
     */
    public function testMatchWithNoCollectionIdentifier()
    {
        $this->assertFalse($this->matcher->match(new BlockView(array('block' => new Block())), array()));
    }
}
