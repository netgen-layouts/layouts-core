<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Parameter;

use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View\ParameterView;
use Netgen\BlockManager\View\Matcher\Parameter\Type;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new Type();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Parameter\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $layout = new ParameterValue(
            array(
                'parameterType' => new TextType(),
            )
        );

        $view = new ParameterView($layout);

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
            array(array('boolean'), false),
            array(array('text'), true),
            array(array('boolean', 'integer'), false),
            array(array('boolean', 'text'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Parameter\Type::match
     */
    public function testMatchWithNoParameterView()
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), array()));
    }
}
