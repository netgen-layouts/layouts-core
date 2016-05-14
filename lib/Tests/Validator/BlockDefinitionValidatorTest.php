<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Validator\BlockDefinitionValidator;
use Netgen\BlockManager\Validator\Constraint\BlockDefinition;

class BlockDefinitionValidatorTest extends ValidatorTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockDefinitionRegistryMock;

    /**
     * @var \Netgen\BlockManager\Validator\BlockDefinitionValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->blockDefinitionRegistryMock = $this->getMock(
            BlockDefinitionRegistryInterface::class
        );

        $this->validator = new BlockDefinitionValidator($this->blockDefinitionRegistryMock);
        $this->validator->initialize($this->executionContextMock);
    }

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

        $this->validator->validate('block_definition', new BlockDefinition());
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

        $this->validator->validate('block_definition', new BlockDefinition());
    }
}
