<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Validator\BlockViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;

class BlockViewTypeValidatorTest extends ValidatorTest
{
    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidate()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('blocks'))
            ->will($this->returnValue(array('block' => array('view_types' => array('large' => array())))));

        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $validator = new BlockViewTypeValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate('large', new BlockViewType(array('definitionIdentifier' => 'block')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidateFailedWithNoBlockDefinition()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('blocks'))
            ->will($this->returnValue(array('block' => array('view_types' => array('large' => array())))));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new BlockViewTypeValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate('large', new BlockViewType(array('definitionIdentifier' => 'other_block')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidateFailedWithNoViewType()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('blocks'))
            ->will($this->returnValue(array('block' => array('view_types' => array('large' => array())))));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new BlockViewTypeValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate('small', new BlockViewType(array('definitionIdentifier' => 'block')));
    }
}
