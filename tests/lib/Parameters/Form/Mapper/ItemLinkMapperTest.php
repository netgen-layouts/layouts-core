<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\ItemLinkType as ItemLinkParameterType;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

class ItemLinkMapperTest extends TestCase
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
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->valueTypeRegistry = new ValueTypeRegistry();
        $this->valueTypeRegistry->addValueType('default', new ValueType(array('isEnabled' => true)));

        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);

        $this->type = new ItemLinkParameterType(
            $this->valueTypeRegistry,
            new RemoteIdConverter($this->itemLoaderMock)
        );

        $this->mapper = new ItemLinkMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserDynamicType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::mapOptions
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
                'item_types' => array('value'),
            ),
            $this->mapper->mapOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::mapOptions
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
                'item_types' => array('default'),
            ),
            $this->mapper->mapOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::handleForm
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

        $this->assertInstanceOf(ItemLinkDataMapper::class, $formBuilder->getDataMapper());
    }
}
