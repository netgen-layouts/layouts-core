<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\BlockViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;

class BlockViewTypeValidatorTest extends ValidatorTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new BlockDefinition(
            'block',
            array('large' => array())
        );

        $this->constraint = new BlockViewType(array('definition' => $this->blockDefinition));

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new BlockViewTypeValidator();
    }

    /**
     * @param string $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'large');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlockDefinition()
    {
        $this->constraint->definition = new stdClass();
        $this->assertValid(true, 'large');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array('large', true),
            array('small', false),
            array('', false),
        );
    }
}
