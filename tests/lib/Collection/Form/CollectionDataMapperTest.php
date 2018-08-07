<?php

declare(strict_types=1);

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

    public function setUp(): void
    {
        $this->mapper = new CollectionDataMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionDataMapper::mapDataToForms
     */
    public function testMapDataToForms(): void
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

        self::assertSame('10', $forms['offset']->getData());
        self::assertSame('5', $forms['limit']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithNoLimit(): void
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

        self::assertSame('10', $forms['offset']->getData());
        self::assertNull($forms['limit']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionDataMapper::mapFormsToData
     */
    public function testMapFormsToData(): void
    {
        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset', 10),
                'limit' => $this->getForm('limit', 5),
            ]
        );

        $data = new CollectionUpdateStruct();

        $this->mapper->mapFormsToData($forms, $data);

        self::assertSame('10', $data->offset);
        self::assertSame('5', $data->limit);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithNoLimit(): void
    {
        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset', 10),
                'limit' => $this->getForm('limit'),
            ]
        );

        $data = new CollectionUpdateStruct();

        $this->mapper->mapFormsToData($forms, $data);

        self::assertSame('10', $data->offset);
        self::assertSame(0, $data->limit);
    }
}
