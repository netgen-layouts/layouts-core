<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\ParameterType\LinkType;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class LinkTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    protected $valueTypeRegistry;

    public function setUp()
    {
        $this->valueTypeRegistry = new ValueTypeRegistry();
        $this->valueTypeRegistry->addValueType('default', new ValueType(array('isEnabled' => true)));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $type = new LinkType($this->valueTypeRegistry);
        $this->assertEquals('link', $type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::configureOptions
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
        return new Parameter(
            array(
                'name' => 'name',
                'type' => new LinkType($this->valueTypeRegistry),
                'options' => $options,
            )
        );
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
                array('value_types' => array('default')),
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
     * @param bool $isRequired
     * @param array $valueTypes
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::getValueConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::getRequiredConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isRequired, $valueTypes, $isValid)
    {
        $type = new LinkType($this->valueTypeRegistry);
        $parameter = $this->getParameter(array('required' => $isRequired, 'value_types' => $valueTypes));
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(null, true, array(), true),
            array(null, false, array(), true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com', 'linkSuffix' => 'suffix')), true, array(), true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com', 'linkSuffix' => 42)), true, array(), false),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com', 'newWindow' => true)), true, array(), true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com', 'newWindow' => false)), true, array(), true),
            array(new LinkValue(array('linkType' => null, 'link' => null)), true, array(), true),
            array(new LinkValue(array('linkType' => null, 'link' => 'http://a.com')), true, array(), false),
            array(new LinkValue(array('linkType' => null, 'link' => null)), false, array(), true),
            array(new LinkValue(array('linkType' => null, 'link' => 'http://a.com')), false, array(), false),
            array(new LinkValue(array('linkType' => 'url', 'link' => null)), true, array(), true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com')), true, array(), true),
            array(new LinkValue(array('linkType' => 'url', 'link' => null)), false, array(), true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com')), false, array(), true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'invalid')), true, array(), false),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'invalid')), false, array(), false),
            array(new LinkValue(array('linkType' => 'email', 'link' => null)), true, array(), true),
            array(new LinkValue(array('linkType' => 'email', 'link' => 'a@a.com')), true, array(), true),
            array(new LinkValue(array('linkType' => 'email', 'link' => null)), false, array(), true),
            array(new LinkValue(array('linkType' => 'email', 'link' => 'a@a.com')), false, array(), true),
            array(new LinkValue(array('linkType' => 'email', 'link' => 'invalid')), true, array(), false),
            array(new LinkValue(array('linkType' => 'email', 'link' => 'invalid')), false, array(), false),
            array(new LinkValue(array('linkType' => 'phone', 'link' => null)), true, array(), true),
            array(new LinkValue(array('linkType' => 'phone', 'link' => 'a@a.com')), true, array(), true),
            array(new LinkValue(array('linkType' => 'phone', 'link' => null)), false, array(), true),
            array(new LinkValue(array('linkType' => 'phone', 'link' => 'a@a.com')), false, array(), true),
            array(new LinkValue(array('linkType' => 'phone', 'link' => 42)), true, array(), false),
            array(new LinkValue(array('linkType' => 'phone', 'link' => 42)), false, array(), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), true, array(), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), true, array(), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'default://42')), true, array(), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), false, array(), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), false, array(), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'default://42')), false, array(), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), true, array(), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), false, array(), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), true, array('value'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), true, array('value'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), false, array('value'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), false, array('value'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), true, array('value'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), false, array('value'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), true, array('other'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), true, array('other'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), false, array('other'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), false, array('other'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), true, array('other'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), false, array('other'), false),
        );
    }

    /**
     * @param mixed $value
     * @param bool $convertedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::toHash
     * @dataProvider toHashProvider
     */
    public function testToHash($value, $convertedValue)
    {
        $type = new LinkType($this->valueTypeRegistry);
        $this->assertEquals($convertedValue, $type->toHash(new Parameter(), $value));
    }

    public function toHashProvider()
    {
        return array(
            array(
                42,
                null,
            ),
            array(
                new LinkValue(
                    array(
                        'linkType' => 'url',
                        'link' => 'http://www.google.com',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    )
                ),
                array(
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ),
            ),
        );
    }

    /**
     * @param mixed $value
     * @param bool $convertedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::fromHash
     * @dataProvider fromHashProvider
     */
    public function testFromHash($value, $convertedValue)
    {
        $type = new LinkType($this->valueTypeRegistry);
        $this->assertEquals($convertedValue, $type->fromHash(new Parameter(), $value));
    }

    public function fromHashProvider()
    {
        return array(
            array(
                42,
                new LinkValue(),
            ),
            array(
                array(),
                new LinkValue(),
            ),
            array(
                array(
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ),
                new LinkValue(
                    array(
                        'linkType' => 'url',
                        'link' => 'http://www.google.com',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    )
                ),
            ),
            array(
                array(
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                ),
                new LinkValue(
                    array(
                        'linkType' => 'url',
                        'link' => 'http://www.google.com',
                    )
                ),
            ),
        );
    }

    /**
     * @param mixed $value
     * @param bool $convertedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::createValueFromInput
     * @dataProvider createValueFromInputProvider
     */
    public function testCreateValueFromInput($value, $convertedValue)
    {
        $type = new LinkType($this->valueTypeRegistry);
        $this->assertEquals($convertedValue, $type->createValueFromInput(new Parameter(), $value));
    }

    public function createValueFromInputProvider()
    {
        return array(
            array(
                42,
                42,
            ),
            array(
                array(),
                array(),
            ),
            array(
                array(
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ),
                new LinkValue(
                    array(
                        'linkType' => 'url',
                        'link' => 'http://www.google.com',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    )
                ),
            ),
            array(
                array(
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                ),
                new LinkValue(
                    array(
                        'linkType' => 'url',
                        'link' => 'http://www.google.com',
                    )
                ),
            ),
        );
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, $isEmpty)
    {
        $type = new LinkType($this->valueTypeRegistry);
        $this->assertEquals($isEmpty, $type->isValueEmpty(new Parameter(), $value));
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
            array(new LinkValue(), true),
            array(new LinkValue(array('linkType' => 'url')), true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://www.google.com')), false),
        );
    }
}
