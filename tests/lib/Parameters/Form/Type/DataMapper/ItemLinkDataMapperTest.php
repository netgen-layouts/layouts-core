<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type\DataMapper;

use ArrayIterator;
use Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\Layouts\Tests\Form\DataMapper\DataMapperTestBase;
use Symfony\Component\Form\FormInterface;

final class ItemLinkDataMapperTest extends DataMapperTestBase
{
    private ItemLinkDataMapper $mapper;

    protected function setUp(): void
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
            ],
        );

        $this->mapper->mapDataToForms('value://42', $forms);

        $itemValueForm = $forms['item_value'];
        $itemTypeForm = $forms['item_type'];

        self::assertInstanceOf(FormInterface::class, $itemValueForm);
        self::assertInstanceOf(FormInterface::class, $itemTypeForm);

        self::assertSame('42', $itemValueForm->getData());
        self::assertSame('value', $itemTypeForm->getData());
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
            ],
        );

        $this->mapper->mapDataToForms('value://', $forms);

        $itemValueForm = $forms['item_value'];
        $itemTypeForm = $forms['item_type'];

        self::assertInstanceOf(FormInterface::class, $itemValueForm);
        self::assertInstanceOf(FormInterface::class, $itemTypeForm);

        self::assertNull($itemValueForm->getData());
        self::assertNull($itemTypeForm->getData());
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
            ],
        );

        $this->mapper->mapDataToForms(42, $forms);

        $itemValueForm = $forms['item_value'];
        $itemTypeForm = $forms['item_type'];

        self::assertInstanceOf(FormInterface::class, $itemValueForm);
        self::assertInstanceOf(FormInterface::class, $itemTypeForm);

        self::assertNull($itemValueForm->getData());
        self::assertNull($itemTypeForm->getData());
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
            ],
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
            ],
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
            ],
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertNull($data);
    }
}
