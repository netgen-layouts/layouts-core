<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\LinkType;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\LinkType as LinkParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

final class LinkMapperTest extends TestCase
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

    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->valueTypeRegistry = new ValueTypeRegistry();
        $this->valueTypeRegistry->addValueType('default', new ValueType(array('isEnabled' => true)));

        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);

        $this->type = new LinkParameterType(
            $this->valueTypeRegistry,
            new RemoteIdConverter($this->itemLoaderMock)
        );

        $this->mapper = new LinkMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(LinkType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::mapOptions
     */
    public function testMapOptions()
    {
        $parameter = new Parameter(
            array(
                'type' => $this->type,
                'options' => array(
                    'value_types' => array('value'),
                ),
            )
        );

        $this->assertEquals(
            array(
                'label' => false,
                'value_types' => array('value'),
            ),
            $this->mapper->mapOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::mapOptions
     */
    public function testMapOptionsWithEmptyValueTypes()
    {
        $parameter = new Parameter(
            array(
                'type' => $this->type,
                'options' => array(
                    'value_types' => array('default'),
                ),
            )
        );

        $this->assertEquals(
            array(
                'label' => false,
                'value_types' => array('default'),
            ),
            $this->mapper->mapOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::handleForm
     */
    public function testHandleForm()
    {
        $parameter = new Parameter(
            array(
                'type' => $this->type,
            )
        );

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $factory = $this->createMock(FormFactoryInterface::class);
        $formBuilder = new FormBuilder('name', null, $dispatcher, $factory);

        $this->mapper->handleForm($formBuilder, $parameter);

        $this->assertInstanceOf(LinkDataMapper::class, $formBuilder->getDataMapper());
    }
}
