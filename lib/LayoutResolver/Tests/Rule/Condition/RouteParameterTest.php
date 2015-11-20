<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Rule\Target;

use Netgen\BlockManager\LayoutResolver\Rule\Condition\RouteParameter;
use Netgen\BlockManager\LayoutResolver\Rule\Target\Route;
use Netgen\BlockManager\LayoutResolver\Rule\TargetInterface;
use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Target;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_TestCase;

class RouteParameterTest extends PHPUnit_Framework_TestCase
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
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition\RouteParameter::matches
     *
     * @param mixed $what
     * @param array $values
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($what, array $values, $matches)
    {
        $condition = new RouteParameter();
        $condition->setRequestStack($this->requestStack);

        $condition->setWhat($what);
        $condition->setValues($values);
        self::assertEquals($matches, $condition->matches());
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
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition\RouteParameter::matches
     */
    public function testMatchesWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $condition = new RouteParameter();
        $condition->setRequestStack($this->requestStack);

        self::assertEquals(false, $condition->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition\RouteParameter::matches
     */
    public function testMatchesWithNoValues()
    {
        $condition = new RouteParameter();
        $condition->setRequestStack($this->requestStack);

        self::assertEquals(false, $condition->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition\RouteParameter::matches
     */
    public function testMatchesWithNoWhat()
    {
        $condition = new RouteParameter();
        $condition->setRequestStack($this->requestStack);

        $condition->setValues(array(42));
        self::assertEquals(false, $condition->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition\RouteParameter::matches
     */
    public function testMatchesWithEmptyWhat()
    {
        $condition = new RouteParameter();
        $condition->setRequestStack($this->requestStack);

        $condition->setValues(array(42));
        $condition->setWhat('');
        self::assertEquals(false, $condition->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition\RouteParameter::supports
     *
     * @param \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface $target
     * @param bool $supports
     *
     * @dataProvider supportsProvider
     */
    public function testSupports(TargetInterface $target, $supports)
    {
        $condition = new RouteParameter();

        self::assertEquals($supports, $condition->supports($target));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Route(), true),
            array(new Target(), false),
        );
    }
}
