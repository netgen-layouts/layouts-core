<?php

namespace Netgen\BlockManager\Tests\View\Matcher;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Matcher\FormName;
use Netgen\BlockManager\Tests\View\Stubs\View;

class FormNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new FormName();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\FormName::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $view = new View(new Value());
        $view->addParameters(array('form_name' => 'full'));

        self::assertEquals($expected, $this->matcher->match($view, $config));
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
            array(array('full'), true),
            array(array('design'), false),
            array(array('design', 'full'), true),
            array(array('full', 'design'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\FormName::match
     */
    public function testMatchWithNoFormName()
    {
        self::assertFalse($this->matcher->match(new View(new Value()), array()));
    }
}
