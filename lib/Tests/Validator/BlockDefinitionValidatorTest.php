<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Validator\BlockDefinitionValidator;
use Netgen\BlockManager\Validator\Constraint\BlockDefinition;

class BlockDefinitionValidatorTest extends ValidatorTest
{
    /**
     * @covers \Netgen\BlockManager\Validator\BlockDefinitionValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockDefinitionValidator::validate
     */
    public function testValidate()
    {
        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('hasBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue(true));

        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $validator = new BlockDefinitionValidator($this->blockDefinitionRegistryMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate('block_definition', new BlockDefinition());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockDefinitionValidator::__construct
     * @covers \Netgen\BlockManager\Validator\BlockDefinitionValidator::validate
     */
    public function testValidateFailed()
    {
        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('hasBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue(false));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new BlockDefinitionValidator($this->blockDefinitionRegistryMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate('block_definition', new BlockDefinition());
    }
}
