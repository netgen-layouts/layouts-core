<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class RouteParameterTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter
     */
    protected $conditionType;

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

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->conditionType = new RouteParameter();
        $this->conditionType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter::getType
     */
    public function testGetType()
    {
        self::assertEquals('route_parameter', $this->conditionType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        self::assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, $matches)
    {
        self::assertEquals($matches, $this->conditionType->matches($value));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter::matches
     */
    public function testMatchesWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->conditionType->matches(array()));
    }

    /**
     * Provider for testing condition type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(array('parameter_name' => 'name', 'parameter_values' => array('one', 'two')), true),
            array(array('parameter_name' => 'name', 'parameter_values' => array('one')), true),
            array(array('parameter_name' => 'name', 'parameter_values' => array('')), false),
            array(array('parameter_name' => 'name', 'parameter_values' => array(array('one'))), false),
            array(array('parameter_name' => 'name', 'parameter_values' => array()), false),
            array(array('parameter_name' => 'name'), false),
            array(array('parameter_name' => 42, 'parameter_values' => array('one', 'two')), false),
            array(array('parameter_name' => 42, 'parameter_values' => array('one')), false),
            array(array('parameter_name' => 42, 'parameter_values' => array('')), false),
            array(array('parameter_name' => 42, 'parameter_values' => array(array('one'))), false),
            array(array('parameter_name' => 42, 'parameter_values' => array()), false),
            array(array('parameter_name' => 42), false),
            array(array('parameter_values' => array('one', 'two')), false),
            array(array('parameter_values' => array('one')), false),
            array(array('parameter_values' => array('')), false),
            array(array('parameter_values' => array(array('one'))), false),
            array(array('parameter_values' => array()), false),
            array(array(), false),
            array(null, false),
        );
    }

    /**
     * Provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
    {
        return array(
            array('not_array', false),
            array(array(), false),
            array(array('parameter_name' => array()), false),
            array(array('parameter_values' => array()), false),
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
}
