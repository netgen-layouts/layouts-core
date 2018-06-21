<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Item\CmsItemLoaderInterface;
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
    private $cmsItemLoaderMock;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType
     */
    private $type;

    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->valueTypeRegistry = new ValueTypeRegistry();
        $this->valueTypeRegistry->addValueType('default', new ValueType(['isEnabled' => true]));

        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->type = new ItemLinkParameterType(
            $this->valueTypeRegistry,
            new RemoteIdConverter($this->cmsItemLoaderMock)
        );

        $this->mapper = new ItemLinkMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(ContentBrowserDynamicType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $parameterDefinition = new ParameterDefinition(
            [
                'type' => $this->type,
                'options' => [
                    'value_types' => ['value'],
                ],
            ]
        );

        $this->assertSame(
            [
                'item_types' => ['value'],
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::mapOptions
     */
    public function testMapOptionsWithEmptyValueTypes(): void
    {
        $parameterDefinition = new ParameterDefinition(
            [
                'type' => $this->type,
                'options' => [
                    'value_types' => ['default'],
                ],
            ]
        );

        $this->assertSame(
            [
                'item_types' => ['default'],
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::handleForm
     */
    public function testHandleForm(): void
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
