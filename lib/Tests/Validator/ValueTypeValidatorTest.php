<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Collection\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Validator\ValueTypeValidator;
use Netgen\BlockManager\Validator\Constraint\ValueType;

class ValueTypeValidatorTest extends ValidatorTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueLoaderRegistryMock;

    /**
     * @var \Netgen\BlockManager\Validator\ValueTypeValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->valueLoaderRegistryMock = $this->getMock(
            ValueLoaderRegistryInterface::class
        );

        $this->validator = new ValueTypeValidator($this->valueLoaderRegistryMock);
        $this->validator->initialize($this->executionContextMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     */
    public function testValidate()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('hasValueLoader')
            ->with($this->equalTo('value'))
            ->will($this->returnValue(true));

        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('value', new ValueType());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     */
    public function testValidateFailed()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('hasValueLoader')
            ->with($this->equalTo('value'))
            ->will($this->returnValue(false));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate('value', new ValueType());
    }
}
