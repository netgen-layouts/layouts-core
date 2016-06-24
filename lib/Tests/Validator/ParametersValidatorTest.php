<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Validator\ParametersValidator;
use Netgen\BlockManager\Validator\Constraint\Parameters;

class ParametersValidatorTest extends ValidatorTest
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface
     */
    protected $parameterFilterRegistry;

    /**
     * @var \Netgen\BlockManager\Validator\ParametersValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Validator\Constraint\Parameters
     */
    protected $constraint;

    public function setUp()
    {
        parent::setUp();

        $this->parameterFilterRegistry = new ParameterFilterRegistry();

        $this->validator = new ParametersValidator($this->parameterFilterRegistry);
        $this->validator->initialize($this->executionContextMock);

        $this->constraint = new Parameters(
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
     * @param string $parameters
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\ParametersValidator::validate
     * @covers \Netgen\BlockManager\Validator\ParametersValidator::buildConstraintFields
     * @dataProvider validateDataProvider
     */
    public function testValidate($parameters, $required, $isValid)
    {
        $this->constraint->required = $required;

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

        $this->validator->validate(
            new BlockCreateStruct(array('parameters' => $parameters)),
        $this->constraint
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
