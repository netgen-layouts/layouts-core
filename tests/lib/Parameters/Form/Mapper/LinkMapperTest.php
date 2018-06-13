<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\LinkType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
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
        $this->valueTypeRegistry->addValueType('default', new ValueType(['isEnabled' => true]));

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
                'label' => false,
                'value_types' => ['value'],
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::mapOptions
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
                'label' => false,
                'value_types' => ['default'],
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::handleForm
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

        $this->assertInstanceOf(LinkDataMapper::class, $formBuilder->getDataMapper());
    }
}
