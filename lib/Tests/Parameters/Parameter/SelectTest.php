<?php

namespace Netgen\BlockManager\Tests\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter\Select;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validation;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Select::getType
     */
    public function testGetType()
    {
        $parameter = $this->getParameter(array('options' => array('One' => 1)));
        self::assertEquals('select', $parameter->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Select::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Select::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameter($options);
        self::assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Select::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Select::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameter($options);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\Parameter\Select
     */
    public function getParameter(array $options = array())
    {
        return new Select($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
    {
        return array(
            array(
                array(
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
                array(
                    'multiple' => false,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'multiple' => false,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
                array(
                    'multiple' => false,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'multiple' => true,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
                array(
                    'multiple' => true,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'multiple' => true,
                    'options' => function () {},
                ),
                array(
                    'multiple' => true,
                    'options' => function () {},
                ),
            ),
        );
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    public function invalidOptionsProvider()
    {
        return array(
            array(
                array(
                    'multiple' => 'true',
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'options' => 'options',
                ),
            ),
            array(
                array(
                    'options' => array(),
                ),
            ),
            array(
                array(
                    'undefined_value' => 'Value',
                ),
            ),
            array(
                array(),
            ),
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Select::getParameterConstraints
     */
    public function testGetParameterConstraints()
    {
        $parameter = $this->getParameter(array('options' => array('One' => 1)));

        self::assertEquals(
            array(
                new Constraints\Choice(
                    array(
                        'choices' => array(1),
                        'multiple' => false,
                    )
                ),
            ),
            $parameter->getConstraints()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Select::getParameterConstraints
     */
    public function testGetParameterConstraintsWithClosure()
    {
        $parameter = $this->getParameter(array('options' => function () {return array('One' => 1);}));

        self::assertEquals(
            array(
                new Constraints\Choice(
                    array(
                        'choices' => array(1),
                        'multiple' => false,
                    )
                ),
            ),
            $parameter->getConstraints()
        );
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $parameter = $this->getParameter(array('options' => array('One' => 1, 'Two' => 2)));
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $parameter->getConstraints());
        self::assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(1, true),
            array('One', false),
            array(2, true),
            array('Two', false),
            array('123abc.ASD', false),
            array(0, false),
        );
    }
}
