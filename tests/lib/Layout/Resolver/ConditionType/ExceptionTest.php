<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\ConditionType;

use Exception;
use Netgen\BlockManager\Layout\Resolver\ConditionType\Exception as ExceptionConditionType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class ExceptionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception
     */
    private $conditionType;

    /**
     * Sets up the route target tests.
     */
    public function setUp()
    {
        $this->conditionType = new ExceptionConditionType();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::getType
     */
    public function testGetType()
    {
        $this->assertEquals('exception', $this->conditionType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, $matches)
    {
        $request = Request::create('/');

        $request->attributes->set('exception', FlattenException::create(new Exception(), 404));

        $this->assertEquals($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::matches
     */
    public function testMatchesWithNoException()
    {
        $request = Request::create('/');

        $this->assertFalse($this->conditionType->matches($request, array(404)));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::matches
     */
    public function testMatchesWithInvalidException()
    {
        $request = Request::create('/');

        $request->attributes->set('exception', new Exception());

        $this->assertFalse($this->conditionType->matches($request, array(404)));
    }

    /**
     * Provider for testing condition type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(array(404), true),
            array(array(404, 403), true),
            array(array(403, 700), false),
            array(array(403, 200), false),
            array(array(700), false),
            array(array(200), false),
            array(array(), true),
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
            array(array(), true),
            array(array(404), true),
            array(array(403), false),
            array(array(404, 403), true),
            array(array(403, 404), true),
            array(array(403, 401), false),
        );
    }
}
