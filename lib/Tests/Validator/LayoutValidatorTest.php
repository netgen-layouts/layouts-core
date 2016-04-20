<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Validator\LayoutValidator;
use Netgen\BlockManager\Validator\Constraint\Layout;

class LayoutValidatorTest extends ValidatorTest
{
    /**
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::validate
     */
    public function testValidate()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('layouts'))
            ->will($this->returnValue(array('layout' => array())));

        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $validator = new LayoutValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate('layout', new Layout());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutValidator::validate
     */
    public function testValidateFailedWithNoLayout()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('layouts'))
            ->will($this->returnValue(array('layout' => array())));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new LayoutValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate('other_layout', new Layout());
    }
}
