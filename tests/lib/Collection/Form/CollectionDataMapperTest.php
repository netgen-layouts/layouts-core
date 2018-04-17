<?php

namespace Netgen\BlockManager\Tests\Collection\Form;

use ArrayIterator;
use Netgen\BlockManager\Collection\Form\CollectionDataMapper;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Tests\Form\DataMapper\DataMapperTest;

final class CollectionDataMapperTest extends DataMapperTest
{
    /**
     * @var \Netgen\BlockManager\Collection\Form\CollectionDataMapper
     */
    private $mapper;

    public function setUp()
    {
        parent::setUp();

        $this->mapper = new CollectionDataMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionDataMapper::mapDataToForms
     */
    public function testMapDataToForms()
    {
        $data = new CollectionUpdateStruct();
        $data->offset = 10;
        $data->limit = 5;

        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset'),
                'limit' => $this->getForm('limit'),
            ]
        );

        $this->mapper->mapDataToForms($data, $forms);

        $this->assertEquals(10, $forms['offset']->getData());
        $this->assertEquals(5, $forms['limit']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithNoLimit()
    {
        $data = new CollectionUpdateStruct();
        $data->offset = 10;
        $data->limit = 0;

        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset'),
                'limit' => $this->getForm('limit'),
            ]
        );

        $this->mapper->mapDataToForms($data, $forms);

        $this->assertEquals(10, $forms['offset']->getData());
        $this->assertNull($forms['limit']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionDataMapper::mapFormsToData
     */
    public function testMapFormsToData()
    {
        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset', 10),
                'limit' => $this->getForm('limit', 5),
            ]
        );

        $data = new CollectionUpdateStruct();

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertEquals(
            new CollectionUpdateStruct(
                [
                    'offset' => 10,
                    'limit' => 5,
                ]
            ),
            $data
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithNoLimit()
    {
        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset', 10),
                'limit' => $this->getForm('limit'),
            ]
        );

        $data = new CollectionUpdateStruct();

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertEquals(
            new CollectionUpdateStruct(
                [
                    'offset' => 10,
                    'limit' => 0,
                ]
            ),
            $data
        );
    }
}
