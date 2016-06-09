<?php

namespace Netgen\BlockManager\Tests\View\Matcher;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\FormView;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\FormType;
use PHPUnit\Framework\TestCase;

class FormNameTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new FormType();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Form\FormType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $view = new FormView();

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
            array(array('other_form_type'), false),
            array(array('form_type'), true),
            array(array('other_form_type', 'second_form_type'), false),
            array(array('form_type', 'other_form_type'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\FormType::match
     */
    public function testMatchWithNoFormView()
    {
        self::assertFalse($this->matcher->match(new View(new Value()), array()));
    }
}
