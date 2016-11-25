<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\BlockItemViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Symfony\Component\Validator\Constraints\NotBlank;
use stdClass;

class BlockItemViewTypeValidatorTest extends ValidatorTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new BlockDefinition(
            'block',
            array('large' => array('standard'))
        );

        $this->constraint = new BlockItemViewType(array('definition' => $this->blockDefinition));

        parent::setUp();
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
     * @param string $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($viewType, $value, $isValid)
    {
        $this->constraint->viewType = $viewType;

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlockDefinition()
    {
        $this->constraint->definition = new stdClass();
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidViewType()
    {
        $this->constraint->viewType = 42;
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->constraint->viewType = 'large';
        $this->assertValid(true, 42);
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
