<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use Netgen\Layouts\Parameters\Form\Mapper\ItemLinkMapper;
use Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Parameters\ParameterType\ItemLinkType as ItemLinkParameterType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

final class ItemLinkMapperTest extends TestCase
{
    private ValueTypeRegistry $valueTypeRegistry;

    private MockObject $cmsItemLoaderMock;

    private ItemLinkParameterType $type;

    private ItemLinkMapper $mapper;

    protected function setUp(): void
    {
        $this->valueTypeRegistry = new ValueTypeRegistry(['default' => ValueType::fromArray(['isEnabled' => true])]);

        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->type = new ItemLinkParameterType(
            $this->valueTypeRegistry,
            new RemoteIdConverter($this->cmsItemLoaderMock),
        );

        $this->mapper = new ItemLinkMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\ItemLinkMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserDynamicType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\ItemLinkMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->type,
                'options' => [
                    'value_types' => ['value'],
                ],
            ],
        );

        self::assertSame(
            [
                'item_types' => ['value'],
            ],
            $this->mapper->mapOptions($parameterDefinition),
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\ItemLinkMapper::mapOptions
     */
    public function testMapOptionsWithEmptyValueTypes(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->type,
                'options' => [
                    'value_types' => ['default'],
                ],
            ],
        );

        self::assertSame(
            [
                'item_types' => ['default'],
            ],
            $this->mapper->mapOptions($parameterDefinition),
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\ItemLinkMapper::handleForm
     */
    public function testHandleForm(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->type,
            ],
        );

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $factory = $this->createMock(FormFactoryInterface::class);
        $formBuilder = new FormBuilder('name', null, $dispatcher, $factory);

        $this->mapper->handleForm($formBuilder, $parameterDefinition);

        self::assertInstanceOf(ItemLinkDataMapper::class, $formBuilder->getDataMapper());
    }
}
