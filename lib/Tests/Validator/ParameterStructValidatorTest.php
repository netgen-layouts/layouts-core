<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterFilter;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Structs\ParameterStructValidator;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;

class ParameterStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->constraint = new ParameterStruct(
            array(
                'parameters' => array(
                    'css_id' => new Parameter\TextLine(array(), true),
                    'checkbox' => new Parameter\Compound\Boolean(
                        array(
                            'param' => new Parameter\Identifier(array(), true),
                        )
                    ),
                ),
            )
        );
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        $parameterTypeRegistry = new ParameterTypeRegistry();
        $parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $parameterTypeRegistry->addParameterType(new ParameterType\IdentifierType());
        $parameterTypeRegistry->addParameterType(new ParameterType\Compound\BooleanType());

        $parameterFilterRegistry = new ParameterFilterRegistry();
        $parameterFilterRegistry->addParameterFilters('text_line', array(new ParameterFilter()));

        return new ParameterStructValidator($parameterTypeRegistry, $parameterFilterRegistry);
    }

    /**
     * @param string $parameters
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\ParameterStructValidator::__construct
     * @covers \Netgen\BlockManager\Validator\ParameterStructValidator::validate
     * @covers \Netgen\BlockManager\Validator\ParameterStructValidator::filterParameters
     * @covers \Netgen\BlockManager\Validator\ParameterStructValidator::buildConstraintFields
     * @covers \Netgen\BlockManager\Validator\ParameterStructValidator::buildFieldConstraint
     * @dataProvider validateDataProvider
     */
    public function testValidate($parameters, $required, $isValid)
    {
        $this->constraint->required = $required;

        $this->assertValid(
            $isValid,
            new BlockCreateStruct(array('parameters' => $parameters))
        );
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
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => null), true, false),
            array(array('css_id' => 'ID', 'checkbox' => true), true, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => ''), true, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => null), true, true),
            array(array('css_id' => 'ID', 'checkbox' => false), true, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => ''), true, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => null), true, true),
            array(array('css_id' => 'ID', 'checkbox' => null), true, false),
            array(array('css_id' => 'ID', 'param' => 'value'), true, false),
            array(array('css_id' => 'ID', 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'param' => ''), true, false),
            array(array('css_id' => 'ID', 'param' => null), true, false),
            array(array('css_id' => 'ID'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => ''), false, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => null), false, false),
            array(array('css_id' => 'ID', 'checkbox' => true), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => ''), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => null), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => ''), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => null), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null), false, true),
            array(array('css_id' => 'ID', 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'param' => ''), false, true),
            array(array('css_id' => 'ID', 'param' => null), false, true),
            array(array('css_id' => 'ID'), false, true),
        );
    }
}
