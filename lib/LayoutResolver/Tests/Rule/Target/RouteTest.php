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
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target\Route::matches
     *
     * @param array $values
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches(array $values, $matches)
    {
        $target = new Route();
        $target->setRequestStack($this->requestStack);

        $target->setValues($values);
        self::assertEquals($matches, $target->matches());
    }

    /**
     * Provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
    {
        return array(
            array(array('my_cool_route'), true),
            array(array('my_other_cool_route'), false),
            array(array('my_cool_route', 'my_other_cool_route'), true),
            array(array('my_other_cool_route', 'my_cool_route'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target\Route::matches
     */
    public function testMatchesWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $target = new Route();
        $target->setRequestStack($this->requestStack);

        self::assertEquals(false, $target->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target\Route::matches
     */
    public function testMatchesWithNoValues()
    {
        $target = new Route();
        $target->setRequestStack($this->requestStack);

        self::assertEquals(true, $target->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target\Route::matches
     */
    public function testEvaluateWithEmptyValues()
    {
        $target = new Route();
        $target->setRequestStack($this->requestStack);

        $target->setValues(array());
        self::assertEquals(true, $target->matches());
    }
}
