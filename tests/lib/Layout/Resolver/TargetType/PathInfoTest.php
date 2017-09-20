<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

class PathInfoTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo
     */
    private $targetType;

    public function setUp()
    {
        $this->targetType = new PathInfo();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo::getType
     */
    public function testGetType()
    {
        $this->assertEquals('path_info', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo::provideValue
     */
    public function testProvideValue()
    {
        $request = Request::create('/the/answer');

        $this->assertEquals(
            '/the/answer',
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
            array('/some/route', true),
            array('/', true),
            array('', false),
            array(null, false),
        );
    }
}
