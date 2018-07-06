<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Type\DataMapper;

use ArrayIterator;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\BlockManager\Tests\Form\DataMapper\DataMapperTest;

final class ItemLinkDataMapperTest extends DataMapperTest
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new ItemLinkDataMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapDataToForms
     */
    public function testMapDataToForms(): void
    {
        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapDataToForms('value://42', $forms);

        $this->assertSame('42', $forms['item_id']->getData());
        $this->assertSame('value', $forms['item_type']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithInvalidData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapDataToForms('value://', $forms);

        $this->assertNull($forms['item_id']->getData());
        $this->assertNull($forms['item_type']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithNonStringData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapDataToForms(42, $forms);

        $this->assertNull($forms['item_id']->getData());
        $this->assertNull($forms['item_type']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapFormsToData
     */
    public function testMapFormsToData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id', '42'),
                'item_type' => $this->getForm('item_type', 'value'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertSame('value://42', $data);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithInvalidItemIdFormData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id'),
                'item_type' => $this->getForm('item_type', 'value'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertNull($data);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithInvalidItemTypeFormData(): void
    {
        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id', '42'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertNull($data);
    }
}
