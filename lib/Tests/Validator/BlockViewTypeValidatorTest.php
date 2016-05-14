<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition as Configuration;
use Netgen\BlockManager\Configuration\BlockDefinition\ViewType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Validator\BlockViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;

class BlockViewTypeValidatorTest extends ValidatorTest
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    /**
     * @var \Netgen\BlockManager\Validator\BlockViewTypeValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->blockDefinition = new BlockDefinition();
        $this->blockDefinition->setConfiguration(
            new Configuration(
                'block',
                array(),
                array(
                    'large' => new ViewType('large', 'Large')
                )
            )
        );

        $this->validator = new BlockViewTypeValidator();
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

        $this->validator->validate('large', new BlockViewType(array('definition' => $this->blockDefinition)));
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

        $this->validator->validate('small', new BlockViewType(array('definition' => $this->blockDefinition)));
    }
}
