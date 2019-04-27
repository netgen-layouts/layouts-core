<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type\DataMapper;

use ArrayIterator;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Parameters\ParameterType\LinkType;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Netgen\Layouts\Tests\Form\DataMapper\DataMapperTest;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;

final class LinkDataMapperTest extends DataMapperTest
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\Layouts\Parameters\Form\Type\DataMapper\LinkDataMapper
     */
    private $mapper;

    protected function setUp(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => new LinkType(
                    new ValueTypeRegistry([]),
                    new RemoteIdConverter($this->createMock(CmsItemLoaderInterface::class))
                ),
            ]
        );

        $this->mapper = new LinkDataMapper($parameterDefinition);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\LinkDataMapper::__construct
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\LinkDataMapper::mapDataToForms
     */
    public function testMapDataToForms(): void
    {
        $linkValue = LinkValue::fromArray(
            [
                'linkType' => 'url',
                'link' => 'http://www.google.com',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ]
        );

        $forms = new ArrayIterator(
            [
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
                'url' => $this->getForm('url'),
            ]
        );

        $this->mapper->mapDataToForms($linkValue, $forms);

        self::assertSame('url', $forms['link_type']->getData());
        self::assertSame('?suffix', $forms['link_suffix']->getData());
        self::assertSame('1', $forms['new_window']->getData());
        self::assertSame('http://www.google.com', $forms['url']->getData());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\LinkDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithInvalidData(): void
    {
        $linkValue = 42;

        $forms = new ArrayIterator(
            [
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
            ]
        );

        $this->mapper->mapDataToForms($linkValue, $forms);

        self::assertNull($forms['link_type']->getData());
        self::assertNull($forms['link_suffix']->getData());
        self::assertNull($forms['new_window']->getData());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\LinkDataMapper::mapFormsToData
     */
    public function testMapFormsToData(): void
    {
        $forms = new ArrayIterator(
            [
                'link_type' => $this->getForm('link_type', 'url'),
                'link_suffix' => $this->getForm('link_suffix', '?suffix'),
                'new_window' => $this->getForm('new_window', '1'),
                'url' => $this->getForm('url', 'http://www.google.com'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertInstanceOf(LinkValue::class, $data);

        self::assertSame(
            [
                'linkType' => 'url',
                'link' => 'http://www.google.com',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
            $this->exportObject($data)
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\LinkDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithInvalidFormData(): void
    {
        $forms = new ArrayIterator(
            [
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertInstanceOf(LinkValue::class, $data);

        self::assertSame(
            [
                'linkType' => null,
                'link' => null,
                'linkSuffix' => null,
                'newWindow' => false,
            ],
            $this->exportObject($data)
        );
    }
}
