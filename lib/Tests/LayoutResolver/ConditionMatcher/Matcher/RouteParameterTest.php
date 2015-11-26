<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\ConditionMatcher\Matcher;

use Netgen\BlockManager\LayoutResolver\ConditionMatcher\Matcher\RouteParameter;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class RouteParameterTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * Sets up the route target tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set(
            '_route_params',
            array(
                'the_answer' => 42,
            )
        );

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Matcher\RouteParameter::getConditionIdentifier
     */
    public function testGetConditionIdentifier()
    {
        $conditionMatcher = new RouteParameter();

        self::assertEquals('route_parameter', $conditionMatcher->getConditionIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Matcher\RouteParameter::matches
     *
     * @param array $parameters
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches(array $parameters, $matches)
    {
        $conditionMatcher = new RouteParameter();
        $conditionMatcher->setRequestStack($this->requestStack);

        self::assertEquals($matches, $conditionMatcher->matches($parameters));
    }

    /**
     * Provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
    {
        return array(
            array(array('parameter_name' => null, 'parameter_values' => array()), false),
            array(array('parameter_name' => null, 'parameter_values' => array(42)), false),
            array(array('parameter_name' => null, 'parameter_values' => array(24)), false),
            array(array('parameter_name' => null, 'parameter_values' => array(42, 24)), false),
            array(array('parameter_name' => null, 'parameter_values' => array(24, 42)), false),
            array(array('parameter_name' => null, 'parameter_values' => array(24, 25)), false),
            array(array('parameter_name' => 'the_answer', 'parameter_values' => array()), false),
            array(array('parameter_name' => 'the_answer', 'parameter_values' => array(42)), true),
            array(array('parameter_name' => 'the_answer', 'parameter_values' => array(24)), false),
            array(array('parameter_name' => 'the_answer', 'parameter_values' => array(42, 24)), true),
            array(array('parameter_name' => 'the_answer', 'parameter_values' => array(24, 42)), true),
            array(array('parameter_name' => 'the_answer', 'parameter_values' => array(24, 25)), false),
            array(array('parameter_name' => 'the_other_answer', 'parameter_values' => array()), false),
            array(array('parameter_name' => 'the_other_answer', 'parameter_values' => array(42)), false),
            array(array('parameter_name' => 'the_other_answer', 'parameter_values' => array(24)), false),
            array(array('parameter_name' => 'the_other_answer', 'parameter_values' => array(42, 24)), false),
            array(array('parameter_name' => 'the_other_answer', 'parameter_values' => array(24, 42)), false),
            array(array('parameter_name' => 'the_other_answer', 'parameter_values' => array(24, 25)), false),
        );
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Matcher\RouteParameter::matches
     */
    public function testMatchesWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $conditionMatcher = new RouteParameter();
        $conditionMatcher->setRequestStack($this->requestStack);

        self::assertEquals(false, $conditionMatcher->matches(array()));
    }
}
