<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Validator\BlockItemViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;

class BlockItemViewTypeValidatorTest extends ValidatorTest
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    /**
     * @var \Netgen\BlockManager\Validator\BlockItemViewTypeValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Validator\Constraint\BlockItemViewType
     */
    protected $constraint;

    public function setUp()
    {
        parent::setUp();

        $config = new Configuration(
            'block',
            array(),
            array(
                'large' => new ViewType(
                    'large',
                    'Large',
                    array(
                        'standard' => new ItemViewType('standard', 'Standard'),
                    )
                ),
            )
        );

        $this->blockDefinition = new BlockDefinition(
            'block',
            $this->getMock(BlockDefinitionHandlerInterface::class),
            $config
        );

        $this->validator = new BlockItemViewTypeValidator();
        $this->validator->initialize($this->executionContextMock);

        $this->constraint = new BlockItemViewType(array('definition' => $this->blockDefinition));
    }

    /**
     * @param string $viewType
     * @param string $itemViewType
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($viewType, $itemViewType, $isValid)
    {
        $this->constraint->viewType = $viewType;

        if ($isValid) {
            $this->executionContextMock
                ->expects($this->never())
                ->method('buildViolation');
        } else {
            $this->executionContextMock
                ->expects($this->once())
                ->method('buildViolation')
                ->will($this->returnValue($this->violationBuilderMock));
        }

        $this->validator->validate($itemViewType, $this->constraint);
    }

    public function validateDataProvider()
    {
        return array(
            array('large', 'standard', true),
            array('large', 'unknown', false),
            array('large', '', false),
            array('small', 'standard', false),
            array('small', 'unknown', false),
            array('small', '', false),
            array('', 'standard', false),
            array('', 'unknown', false),
            array('', '', false),
        );
    }
}
