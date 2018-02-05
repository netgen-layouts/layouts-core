<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\LinkType;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class LinkTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    private $valueTypeRegistry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\LinkType
     */
    private $type;

    public function setUp()
    {
        $this->valueTypeRegistry = new ValueTypeRegistry();
        $this->valueTypeRegistry->addValueType('default', new ValueType(array('isEnabled' => true)));

        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);

        $this->type = new LinkType($this->valueTypeRegistry, new RemoteIdConverter($this->itemLoaderMock));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('link', $this->type->getIdentifier());
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
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    public function getParameter($options = array())
    {
        return new ParameterDefinition(
            array(
                'name' => 'name',
                'type' => $this->type,
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
                array('value_types' => array('default'), 'allow_invalid_internal' => false),
            ),
            array(
                array('value_types' => array('value')),
                array('value_types' => array('value'), 'allow_invalid_internal' => false),
            ),
            array(
                array('allow_invalid_internal' => false),
                array('value_types' => array('default'), 'allow_invalid_internal' => false),
            ),
            array(
                array('allow_invalid_internal' => true),
                array('value_types' => array('default'), 'allow_invalid_internal' => true),
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
                    'allow_invalid_internal' => 1,
                ),
                array(
                    'allow_invalid_internal' => 0,
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
        $parameter = $this->getParameter(array('required' => $isRequired, 'value_types' => $valueTypes));
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
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
        $this->assertEquals($convertedValue, $this->type->toHash(new ParameterDefinition(), $value));
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
        $this->assertEquals($convertedValue, $this->type->fromHash(new ParameterDefinition(), $value));
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::export
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     * @dataProvider exportProvider
     */
    public function testExport($value, $convertedValue)
    {
        $this->itemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo('42'), $this->equalTo('ezlocation'))
            ->will(
                $this->returnValue(
                    new Item(
                        array(
                            'value' => 42,
                            'remoteId' => 'abc',
                        )
                    )
                )
            );

        $this->assertEquals($convertedValue, $this->type->export(new ParameterDefinition(), $value));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::export
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testExportWithConverterException()
    {
        $this->itemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo('24'), $this->equalTo('ezlocation'))
            ->will($this->throwException(new ItemException()));

        $this->assertEquals(
            array(
                'link_type' => 'internal',
                'link' => 'null://0',
                'link_suffix' => '?suffix',
                'new_window' => true,
            ),
            $this->type->export(
                new ParameterDefinition(),
                new LinkValue(
                    array(
                        'linkType' => 'internal',
                        'link' => 'ezlocation://24',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    )
                )
            )
        );
    }

    public function exportProvider()
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
            array(
                new LinkValue(
                    array(
                        'linkType' => 'internal',
                        'link' => 'ezlocation://42',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    )
                ),
                array(
                    'link_type' => 'internal',
                    'link' => 'ezlocation://abc',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ),
            ),
            array(
                new LinkValue(
                    array(
                        'linkType' => 'internal',
                        'link' => 'invalid',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    )
                ),
                array(
                    'link_type' => 'internal',
                    'link' => 'null://0',
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::import
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     * @dataProvider importProvider
     */
    public function testImport($value, $convertedValue)
    {
        $this->itemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->with($this->equalTo('abc'), $this->equalTo('ezlocation'))
            ->will(
                $this->returnValue(
                    new Item(
                        array(
                            'value' => 42,
                            'remoteId' => 'abc',
                        )
                    )
                )
            );

        $this->assertEquals($convertedValue, $this->type->import(new ParameterDefinition(), $value));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::import
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testImportWithConverterException()
    {
        $this->itemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->with($this->equalTo('def'), $this->equalTo('ezlocation'))
            ->will($this->throwException(new ItemException()));

        $this->assertEquals(
            new LinkValue(
                array(
                    'linkType' => 'internal',
                    'link' => 'null://0',
                    'linkSuffix' => '?suffix',
                    'newWindow' => true,
                )
            ),
            $this->type->import(
                new ParameterDefinition(),
                array(
                    'link_type' => 'internal',
                    'link' => 'ezlocation://def',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                )
            )
        );
    }

    public function importProvider()
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
                        'linkSuffix' => null,
                        'newWindow' => false,
                    )
                ),
            ),
            array(
                array(
                    'link_type' => 'internal',
                    'link' => 'ezlocation://abc',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ),
                new LinkValue(
                    array(
                        'linkType' => 'internal',
                        'link' => 'ezlocation://42',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    )
                ),
            ),
            array(
                array(
                    'link_type' => 'internal',
                    'link' => 'invalid',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ),
                new LinkValue(
                    array(
                        'linkType' => 'internal',
                        'link' => 'null://0',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    )
                ),
            ),
            array(
                array(
                    'link_type' => 'internal',
                ),
                new LinkValue(
                    array(
                        'linkType' => 'internal',
                        'link' => 'null://0',
                        'linkSuffix' => null,
                        'newWindow' => false,
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
        $this->assertEquals($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
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
