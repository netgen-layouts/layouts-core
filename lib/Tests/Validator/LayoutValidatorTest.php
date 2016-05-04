<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Validator\LayoutValidator;
use Netgen\BlockManager\Validator\Constraint\Layout;

class LayoutValidatorTest extends ValidatorTest
{
    /**
     * @var \Netgen\BlockManager\Validator\LayoutValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('layouts'))
            ->will($this->returnValue(array('layout' => array())));

        $this->validator = new LayoutValidator($this->configurationMock);
        $this->validator->initialize($this->executionContextMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::validate
     */
    public function testValidate()
    {
        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('layout', new Layout());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::validate
     */
    public function testValidateFailedWithNoLayout()
    {
        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate('other_layout', new Layout());
    }
}
