<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\PathInfoPrefix;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class PathInfoPrefixTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfoPrefix
     */
    private $targetType;

    public function setUp()
    {
        $this->targetType = new PathInfoPrefix();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfoPrefix::getType
     */
    public function testGetType()
    {
        $this->assertEquals('path_info_prefix', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfoPrefix::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfoPrefix::provideValue
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
        return [
            ['/some/route', true],
            ['/', true],
            ['', false],
            [null, false],
        ];
    }
}
