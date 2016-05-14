<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Configuration\LayoutType\Registry;
use Netgen\BlockManager\Validator\LayoutValidator;
use Netgen\BlockManager\Validator\Constraint\Layout;

class LayoutValidatorTest extends ValidatorTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutTypeRegistryMock;

    /**
     * @var \Netgen\BlockManager\Validator\LayoutValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->layoutTypeRegistryMock = $this->getMock(Registry::class);

        $this->validator = new LayoutValidator($this->layoutTypeRegistryMock);
        $this->validator->initialize($this->executionContextMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::validate
     */
    public function testValidate()
    {
        $this->layoutTypeRegistryMock
            ->expects($this->any())
            ->method('hasLayoutType')
            ->with($this->equalTo('layout_type'))
            ->will($this->returnValue(true));

        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('layout_type', new Layout());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::validate
     */
    public function testValidateFailedWithNoLayout()
    {
        $this->layoutTypeRegistryMock
            ->expects($this->any())
            ->method('hasLayoutType')
            ->with($this->equalTo('layout_type'))
            ->will($this->returnValue(false));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate('layout_type', new Layout());
    }
}
