<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Rule\Target;

use Netgen\BlockManager\LayoutResolver\Rule\Target\Route;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_TestCase;

class RouteTest extends PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * Sets up the route target tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'my_cool_route');

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target\Route::evaluate
     *
     * @param array $values
     * @param bool $evaluates
     *
     * @dataProvider evaluateProvider
     */
    public function testEvaluate(array $values, $evaluates)
    {
        $target = new Route();
        $target->setRequestStack($this->requestStack);

        // Make sure conditions are empty, so we can use matches() method
        // to test the evaluate() method
        $target->setConditions(array());

        $target->setValues($values);
        self::assertEquals($evaluates, $target->matches());
    }

    /**
     * Provider for {@link self::testEvaluate}.
     *
     * @return array
     */
    public function evaluateProvider()
    {
        return array(
            array(array('my_cool_route'), true),
            array(array('my_other_cool_route'), false),
            array(array('my_cool_route', 'my_other_cool_route'), true),
            array(array('my_other_cool_route', 'my_cool_route'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target\Route::evaluate
     */
    public function testEvaluateWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $target = new Route();
        $target->setRequestStack($this->requestStack);

        // Make sure conditions are empty, so we can use matches() method
        // to test the evaluate() method
        $target->setConditions(array());

        self::assertEquals(false, $target->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target\Route::evaluate
     */
    public function testEvaluateWithNoValues()
    {
        $target = new Route();
        $target->setRequestStack($this->requestStack);

        // Make sure conditions are empty, so we can use matches() method
        // to test the evaluate() method
        $target->setConditions(array());

        self::assertEquals(true, $target->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target\Route::evaluate
     */
    public function testEvaluateWithEmptyValues()
    {
        $target = new Route();
        $target->setRequestStack($this->requestStack);

        // Make sure conditions are empty, so we can use matches() method
        // to test the evaluate() method
        $target->setConditions(array());

        $target->setValues(array());
        self::assertEquals(true, $target->matches());
    }
}
