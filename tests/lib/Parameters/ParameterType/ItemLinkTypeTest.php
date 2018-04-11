<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\ItemLinkType;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ItemLinkTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    private $valueTypeRegistry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    public function setUp()
    {
        $this->valueTypeRegistry = new ValueTypeRegistry();
        $this->valueTypeRegistry->addValueType('default', new ValueType(array('isEnabled' => true)));

        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
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

        $this->type = new ItemLinkType($this->valueTypeRegistry, new RemoteIdConverter($this->itemLoaderMock));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('item_link', $this->type->getIdentifier());
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
        $parameter = $this->getParameterDefinition($options);
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
        $this->getParameterDefinition($options);
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
                array('value_types' => array('default'), 'allow_invalid' => false),
            ),
            array(
                array('value_types' => array('value')),
                array('value_types' => array('value'), 'allow_invalid' => false),
            ),
            array(
                array('allow_invalid' => false),
                array('value_types' => array('default'), 'allow_invalid' => false),
            ),
            array(
                array('allow_invalid' => true),
                array('value_types' => array('default'), 'allow_invalid' => true),
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
                    'allow_invalid' => 0,
                ),
                array(
                    'allow_invalid' => 1,
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
        $parameter = $this->getParameterDefinition();
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
            array(null, true),
            array('42', false),
            array('value://42', false),
            array('default://42', true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::export
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testExport()
    {
        $this->assertEquals('ezlocation://abc', $this->type->export($this->getParameterDefinition(), 'ezlocation://42'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::import
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testImport()
    {
        $this->assertEquals('ezlocation://42', $this->type->import($this->getParameterDefinition(), 'ezlocation://abc'));
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
        $this->assertEquals($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
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
