<?php

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

    public function setUp()
    {
        $this->matcher = new Locale();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Block\Locale::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $block = new Block(
            array(
                'locale' => 'en',
            )
        );

        $view = new BlockView(
            array(
                'block' => $block,
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
            array(array('fr'), false),
            array(array('en'), true),
            array(array('fr', 'de'), false),
            array(array('fr', 'en'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\Locale::match
     */
    public function testMatchWithNoBlockView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }
}
