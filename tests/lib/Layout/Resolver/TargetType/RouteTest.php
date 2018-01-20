<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\Route;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class RouteTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\Route
     */
    private $targetType;

    public function setUp()
    {
        $this->targetType = new Route();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::getType
     */
    public function testGetType()
    {
        $this->assertEquals('route', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::provideValue
     */
    public function testProvideValue()
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'my_cool_route');

        $this->assertEquals(
            'my_cool_route',
            $this->targetType->provideValue($request)
        );
    }

    /**
     * Provider for testing target type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('route_name', true),
            array('', false),
            array(null, false),
        );
    }
}
