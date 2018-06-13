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

    public function setUp()
    {
        parent::setUp();

        $this->mapper = new ItemLinkDataMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapDataToForms
     */
    public function testMapDataToForms()
    {
        $data = 'value://42';

        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapDataToForms($data, $forms);

        $this->assertEquals('42', $forms['item_id']->getData());
        $this->assertEquals('value', $forms['item_type']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithInvalidData()
    {
        $data = 'value';

        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapDataToForms($data, $forms);

        $this->assertNull($forms['item_id']->getData());
        $this->assertNull($forms['item_type']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapFormsToData
     */
    public function testMapFormsToData()
    {
        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id', '42'),
                'item_type' => $this->getForm('item_type', 'value'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertEquals('value://42', $data);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithInvalidFormData()
    {
        $forms = new ArrayIterator(
            [
                'item_id' => $this->getForm('item_id'),
                'item_type' => $this->getForm('item_type'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertNull($data);
    }
}
