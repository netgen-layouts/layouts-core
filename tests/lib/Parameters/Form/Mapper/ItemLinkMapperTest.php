<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\ItemLinkType as ItemLinkParameterType;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

final class ItemLinkMapperTest extends TestCase
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
     * @var \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType
     */
    private $type;

    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->valueTypeRegistry = new ValueTypeRegistry();
        $this->valueTypeRegistry->addValueType('default', new ValueType(['isEnabled' => true]));

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
        $parameterDefinition = new ParameterDefinition(
            [
                'type' => $this->type,
                'options' => [
                    'value_types' => ['value'],
                ],
            ]
        );

        $this->assertEquals(
            [
                'item_types' => ['value'],
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::mapOptions
     */
    public function testMapOptionsWithEmptyValueTypes()
    {
        $parameterDefinition = new ParameterDefinition(
            [
                'type' => $this->type,
                'options' => [
                    'value_types' => ['default'],
                ],
            ]
        );

        $this->assertEquals(
            [
                'item_types' => ['default'],
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::handleForm
     */
    public function testHandleForm()
    {
        $parameterDefinition = new ParameterDefinition(
            [
                'type' => $this->type,
            ]
        );

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $factory = $this->createMock(FormFactoryInterface::class);
        $formBuilder = new FormBuilder('name', null, $dispatcher, $factory);

        $this->mapper->handleForm($formBuilder, $parameterDefinition);

        $this->assertInstanceOf(ItemLinkDataMapper::class, $formBuilder->getDataMapper());
    }
}
