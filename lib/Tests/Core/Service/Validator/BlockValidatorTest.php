<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionWithRequiredParameter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition as BlockDefinitionStub;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BlockValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockDefinitionRegistryMock;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\BlockValidator
     */
    protected $blockValidator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->validatorMock = $this->getMock(ValidatorInterface::class);
        $this->blockDefinitionRegistryMock = $this->getMock(BlockDefinitionRegistryInterface::class);

        $this->blockValidator = new BlockValidator($this->blockDefinitionRegistryMock);
        $this->blockValidator->setValidator($this->validatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::validateBlockCreateStruct
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::buildParameterValidationFields
     */
    public function testValidateBlockCreateStruct()
    {
        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue(new BlockDefinitionWithRequiredParameter()));

        $this->validatorMock
            ->expects($this->at(0))
            ->method('validate')
            ->with(
                $this->equalTo('block_definition'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(1))
            ->method('validate')
            ->with(
                $this->equalTo('large'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                    new BlockViewType(array('definition' => new BlockDefinitionWithRequiredParameter())),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(2))
            ->method('validate')
            ->with(
                $this->equalTo('My block'),
                array(
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(3))
            ->method('validate')
            ->with(
                $this->equalTo(array('css_class' => 'class')),
                array(
                    new Constraints\Collection(
                        array(
                            'fields' => array(
                                'css_class' => new Constraints\Required(array(new Constraints\NotBlank())),
                                'css_id' => new Constraints\Optional(array()),
                            ),
                        )
                    ),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'block_definition';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameters(array('css_class' => 'class'));

        $this->blockValidator->validateBlockCreateStruct($blockCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::validateBlockUpdateStruct
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::buildParameterValidationFields
     */
    public function testValidateBlockUpdateStruct()
    {
        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue(new BlockDefinitionStub()));

        $this->validatorMock
            ->expects($this->at(0))
            ->method('validate')
            ->with(
                $this->equalTo('large'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                    new BlockViewType(array('definition' => new BlockDefinitionStub())),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(1))
            ->method('validate')
            ->with(
                $this->equalTo('My block'),
                array(
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(2))
            ->method('validate')
            ->with(
                $this->equalTo(array('css_class' => 'class')),
                array(
                    new Constraints\Collection(
                        array(
                            'fields' => array(
                                'css_class' => new Constraints\Optional(array()),
                                'css_id' => new Constraints\Optional(array()),
                            ),
                        )
                    ),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->viewType = 'large';
        $blockUpdateStruct->name = 'My block';
        $blockUpdateStruct->setParameters(array('css_class' => 'class'));

        $block = new Block(
            array(
                'definitionIdentifier' => 'block_definition',
            )
        );

        $this->blockValidator->validateBlockUpdateStruct($block, $blockUpdateStruct);
    }
}
