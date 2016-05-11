<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Validator\BlockViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;

class BlockViewTypeValidatorTest extends ValidatorTest
{
    /**
     * @var \Netgen\BlockManager\Validator\BlockViewTypeValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('block_definitions'))
            ->will($this->returnValue(array('block' => array('view_types' => array('large' => array())))));

        $this->validator = new BlockViewTypeValidator($this->configurationMock);
        $this->validator->initialize($this->executionContextMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidate()
    {
        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('large', new BlockViewType(array('definitionIdentifier' => 'block')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidateFailedWithNoBlockDefinition()
    {
        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate('large', new BlockViewType(array('definitionIdentifier' => 'other_block')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidateFailedWithNoViewType()
    {
        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate('small', new BlockViewType(array('definitionIdentifier' => 'block')));
    }
}
