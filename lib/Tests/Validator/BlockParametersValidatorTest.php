<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Tests\BlockDefinition\Stubs\BlockDefinition;
use Netgen\BlockManager\Validator\BlockParametersValidator;
use Netgen\BlockManager\Validator\Constraint\BlockParameters;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationList;

class BlockParametersValidatorTest extends ValidatorTest
{
    /**
     * @covers \Netgen\BlockManager\Validator\BlockParametersValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockParametersValidator::validate
     */
    public function testValidate()
    {
        $blockDefinition = new BlockDefinition();

        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue($blockDefinition));

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($this->equalTo('CSS ID'), $this->equalTo(array(new NotBlank())))
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->executionContextMock
            ->expects($this->never())
            ->method('buildValidation');

        $validator = new BlockParametersValidator(
            $this->blockDefinitionRegistryMock,
            $this->validatorMock
        );
        $validator->initialize($this->executionContextMock);

        $validator->validate(
            $blockDefinition->getParameterNames(),
            new BlockParameters(array('definitionIdentifier' => 'block_definition'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockParametersValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockParametersValidator::validate
     */
    public function testValidateFailed()
    {
        $blockDefinition = new BlockDefinition();

        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue($blockDefinition));

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($this->equalTo('CSS ID'), $this->equalTo(array(new NotBlank())))
            ->will($this->returnValue(new ConstraintViolationList(array($this->constraintViolationMock))));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new BlockParametersValidator(
            $this->blockDefinitionRegistryMock,
            $this->validatorMock
        );
        $validator->initialize($this->executionContextMock);

        $validator->validate(
            $blockDefinition->getParameterNames(),
            new BlockParameters(array('definitionIdentifier' => 'block_definition'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockParametersValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockParametersValidator::validate
     */
    public function testValidateFailedWithParameterMissing()
    {
        $blockDefinition = new BlockDefinition();

        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue($blockDefinition));

        $this->validatorMock
            ->expects($this->never())
            ->method('validate');

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new BlockParametersValidator(
            $this->blockDefinitionRegistryMock,
            $this->validatorMock
        );
        $validator->initialize($this->executionContextMock);

        $blockParameters = $blockDefinition->getParameterNames();
        unset($blockParameters['css_id']);

        $validator->validate(
            $blockParameters,
            new BlockParameters(array('definitionIdentifier' => 'block_definition'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockParametersValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockParametersValidator::validate
     */
    public function testValidateFailedWithExcessParameter()
    {
        $blockDefinition = new BlockDefinition();

        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue($blockDefinition));

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($this->equalTo('CSS ID'), $this->equalTo(array(new NotBlank())))
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new BlockParametersValidator(
            $this->blockDefinitionRegistryMock,
            $this->validatorMock
        );
        $validator->initialize($this->executionContextMock);

        $blockParameters = $blockDefinition->getParameterNames();
        $blockParameters['some_param'] = 'some_value';

        $validator->validate(
            $blockParameters,
            new BlockParameters(array('definitionIdentifier' => 'block_definition'))
        );
    }
}
