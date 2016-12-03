<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType\ItemLinkType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ItemLinkTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $type = new ItemLinkType();
        $this->assertEquals('item_link', $type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameter($options);
        $this->assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter($options = array())
    {
        return new Parameter('name', new ItemLinkType(), $options);
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
                array(),
                array('value_types' => array()),
            ),
            array(
                array('value_types' => array('value')),
                array('value_types' => array('value')),
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
                    'value_types' => 42,
                ),
                array(
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::getValueConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::getRequiredConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $type = new ItemLinkType();
        $parameter = $this->getParameter();
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(null, true),
            array('42', false),
            array('value://42', true),
        );
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, $isEmpty)
    {
        $type = new ItemLinkType();
        $this->assertEquals($isEmpty, $type->isValueEmpty($value));
    }

    /**
     * Provider for testing if the value is empty.
     *
     * @return array
     */
    public function emptyProvider()
    {
        return array(
            array(null, true),
            array('', true),
            array('value', true),
            array('value://42', false),
        );
    }
}
