<?php

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterFilter;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Structs\ParameterStructValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ParameterStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $compoundParameter = new CompoundParameterDefinition(
            array(
                'name' => 'checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
            )
        );

        $compoundParameter->setParameterDefinitions(
            array(
                'param' => new ParameterDefinition(
                    array(
                        'name' => 'param',
                        'type' => new ParameterType\IdentifierType(),
                        'isRequired' => true,
                    )
                ),
            )
        );

        $this->constraint = new ParameterStruct(
            array(
                'parameterCollection' => new ParameterCollection(
                    array(
                        'css_id' => new ParameterDefinition(
                            array(
                                'name' => 'css_id',
                                'type' => new ParameterType\TextLineType(),
                                'isRequired' => true,
                            )
                        ),
                        'checkbox' => $compoundParameter,
                    )
                ),
                'allowMissingFields' => true,
            )
        );

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        $parameterFilterRegistry = new ParameterFilterRegistry();
        $parameterFilterRegistry->addParameterFilter('text_line', new ParameterFilter());

        return new ParameterStructValidator($parameterFilterRegistry);
    }

    /**
     * @param string $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::__construct
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::filterParameters
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::buildConstraintFields
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $required, $isValid)
    {
        $this->constraint->allowMissingFields = !$required;

        $this->assertValid(
            $isValid,
            new BlockCreateStruct(array('parameterValues' => $value))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\ParameterStruct", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => 'value'), true, true),
            array(array('css_id' => '', 'checkbox' => true, 'param' => 'value'), true, false),
            array(array('css_id' => null, 'checkbox' => true, 'param' => 'value'), true, false),
            array(array('checkbox' => true, 'param' => 'value'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => 'value'), false, true),
            array(array('css_id' => '', 'checkbox' => true, 'param' => 'value'), false, false),
            array(array('css_id' => null, 'checkbox' => true, 'param' => 'value'), false, false),
            array(array('checkbox' => true, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => ''), true, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => null), true, true),
            array(array('css_id' => 'ID', 'checkbox' => true), true, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => ''), true, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => null), true, true),
            array(array('css_id' => 'ID', 'checkbox' => false), true, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => ''), true, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => null), true, true),
            array(array('css_id' => 'ID', 'checkbox' => null), true, true),
            array(array('css_id' => 'ID', 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'param' => ''), true, false),
            array(array('css_id' => 'ID', 'param' => null), true, true),
            array(array('css_id' => 'ID'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => ''), false, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => null), false, true),
            array(array('css_id' => 'ID', 'checkbox' => true), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => ''), false, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => null), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => ''), false, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => null), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null), false, true),
            array(array('css_id' => 'ID', 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'param' => ''), false, false),
            array(array('css_id' => 'ID', 'param' => null), false, true),
            array(array('css_id' => 'ID'), false, true),
        );
    }
}
