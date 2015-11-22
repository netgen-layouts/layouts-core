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
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Matcher\RouteParameter::getIdentifier
     */
    public function testGetIdentifier()
    {
        $conditionMatcher = new RouteParameter();

        self::assertEquals('route_parameter', $conditionMatcher->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Matcher\RouteParameter::matches
     *
     * @param string $valueIdentifier
     * @param array $values
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($valueIdentifier, array $values, $matches)
    {
        $conditionMatcher = new RouteParameter();
        $conditionMatcher->setRequestStack($this->requestStack);

        self::assertEquals($matches, $conditionMatcher->matches($valueIdentifier, $values));
    }

    /**
     * Provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
    {
        return array(
            array(null, array(42), false),
            array(null, array(24), false),
            array(null, array(42, 24), false),
            array(null, array(24, 42), false),
            array('the_answer', array(42), true),
            array('the_answer', array(24), false),
            array('the_answer', array(42, 24), true),
            array('the_answer', array(24, 42), true),
            array('the_other_answer', array(42), false),
            array('the_other_answer', array(24), false),
            array('the_other_answer', array(42, 24), false),
            array('the_other_answer', array(24, 42), false),
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

        self::assertEquals(false, $conditionMatcher->matches('identifier', array(42)));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Matcher\RouteParameter::matches
     */
    public function testMatchesWithEmptyIdentifier()
    {
        $conditionMatcher = new RouteParameter();
        $conditionMatcher->setRequestStack($this->requestStack);

        self::assertEquals(false, $conditionMatcher->matches('', array(42)));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Matcher\RouteParameter::matches
     */
    public function testMatchesWithEmptyValues()
    {
        $conditionMatcher = new RouteParameter();
        $conditionMatcher->setRequestStack($this->requestStack);

        self::assertEquals(false, $conditionMatcher->matches('identifier', array()));
    }
}
