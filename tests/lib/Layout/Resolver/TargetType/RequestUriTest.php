<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validation;

class RequestUriTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/the/answer', 'GET', array('a' => 42));

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->targetType = new RequestUri();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::getType
     */
    public function testGetType()
    {
        $this->assertEquals('request_uri', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::provideValue
     */
    public function testProvideValue()
    {
        $this->assertEquals(
            '/the/answer?a=42',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $this->assertNull($this->targetType->provideValue());
    }

    /**
     * Provider for testing target type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('/some/route?id=42', true),
            array('/', true),
            array('', false),
            array(null, false),
        );
    }
}
