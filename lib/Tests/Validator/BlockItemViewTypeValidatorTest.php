<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\BlockItemViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;

class BlockItemViewTypeValidatorTest extends ValidatorTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    public function setUp()
    {
        parent::setUp();

        $this->blockDefinition = new BlockDefinition(
            'block',
            array('large' => array('standard'))
        );

        $this->constraint = new BlockItemViewType(array('definition' => $this->blockDefinition));
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        return new BlockItemViewTypeValidator();
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

        $this->assertValid($isValid, $itemViewType);
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
