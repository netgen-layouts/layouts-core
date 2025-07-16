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
use Netgen\Layouts\Tests\Form\DataMapper\DataMapperTestBase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Symfony\Component\Form\FormInterface;

final class LinkDataMapperTest extends DataMapperTestBase
{
    use ExportObjectTrait;

    private LinkDataMapper $mapper;

    protected function setUp(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => new LinkType(
                    new ValueTypeRegistry([]),
                    new RemoteIdConverter($this->createMock(CmsItemLoaderInterface::class)),
                ),
            ],
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
                'link' => 'https://netgen.io',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
        );

        $forms = new ArrayIterator(
            [
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
                'url' => $this->getForm('url'),
            ],
        );

        $this->mapper->mapDataToForms($linkValue, $forms);

        $linkTypeForm = $forms['link_type'];
        $linkSuffixForm = $forms['link_suffix'];
        $newWindowForm = $forms['new_window'];
        $urlForm = $forms['url'];

        self::assertInstanceOf(FormInterface::class, $linkTypeForm);
        self::assertInstanceOf(FormInterface::class, $linkSuffixForm);
        self::assertInstanceOf(FormInterface::class, $newWindowForm);
        self::assertInstanceOf(FormInterface::class, $urlForm);

        self::assertSame('url', $linkTypeForm->getData());
        self::assertSame('?suffix', $linkSuffixForm->getData());
        self::assertSame('1', $newWindowForm->getData());
        self::assertSame('https://netgen.io', $urlForm->getData());
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
            ],
        );

        $this->mapper->mapDataToForms($linkValue, $forms);

        $linkTypeForm = $forms['link_type'];
        $linkSuffixForm = $forms['link_suffix'];
        $newWindowForm = $forms['new_window'];

        self::assertInstanceOf(FormInterface::class, $linkTypeForm);
        self::assertInstanceOf(FormInterface::class, $linkSuffixForm);
        self::assertInstanceOf(FormInterface::class, $newWindowForm);

        self::assertNull($linkTypeForm->getData());
        self::assertNull($linkSuffixForm->getData());
        self::assertNull($newWindowForm->getData());
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
                'url' => $this->getForm('url', 'https://netgen.io'),
            ],
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertInstanceOf(LinkValue::class, $data);

        self::assertSame(
            [
                'link' => 'https://netgen.io',
                'linkSuffix' => '?suffix',
                'linkType' => 'url',
                'newWindow' => true,
            ],
            $this->exportObject($data),
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
            ],
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertInstanceOf(LinkValue::class, $data);

        self::assertSame(
            [
                'link' => '',
                'linkSuffix' => '',
                'linkType' => '',
                'newWindow' => false,
            ],
            $this->exportObject($data),
        );
    }
}
