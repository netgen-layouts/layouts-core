<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type\DataMapper;

use ArrayIterator;
use Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\Layouts\Tests\Form\DataMapper\DataMapperTest;

final class ItemLinkDataMapperTest extends DataMapperTest
{
    /**
     * @var \Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new ItemLinkDataMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapDataToForms
     */
    public function testMapDataToForms(): void
    {
        $forms = new ArrayIterator(
            [
                'item_value' => $this->getForm('item_value'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapDataToForms('value://42', $forms);

        self::assertSame('42', $forms['item_value']->getData());
        self::assertSame('value', $forms['item_type']->getData());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithInvalidData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_value' => $this->getForm('item_value'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapDataToForms('value://', $forms);

        self::assertNull($forms['item_value']->getData());
        self::assertNull($forms['item_type']->getData());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithNonStringData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_value' => $this->getForm('item_value'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapDataToForms(42, $forms);

        self::assertNull($forms['item_value']->getData());
        self::assertNull($forms['item_type']->getData());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapFormsToData
     */
    public function testMapFormsToData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_value' => $this->getForm('item_value', '42'),
                'item_type' => $this->getForm('item_type', 'value'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertSame('value://42', $data);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithInvalidItemValueFormData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_value' => $this->getForm('item_value'),
                'item_type' => $this->getForm('item_type', 'value'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertNull($data);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithInvalidItemTypeFormData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_value' => $this->getForm('item_value', '42'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertNull($data);
    }
}
