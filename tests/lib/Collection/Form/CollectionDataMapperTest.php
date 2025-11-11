<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Form;

use ArrayIterator;
use Netgen\Layouts\API\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\Collection\Form\CollectionDataMapper;
use Netgen\Layouts\Tests\Form\DataMapper\DataMapperTestBase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\FormInterface;

#[CoversClass(CollectionDataMapper::class)]
final class CollectionDataMapperTest extends DataMapperTestBase
{
    private CollectionDataMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new CollectionDataMapper();
    }

    public function testMapDataToForms(): void
    {
        $data = new CollectionUpdateStruct();
        $data->offset = 10;
        $data->limit = 5;

        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset'),
                'limit' => $this->getForm('limit'),
            ],
        );

        $this->mapper->mapDataToForms($data, $forms);

        $offsetForm = $forms['offset'];
        $limitForm = $forms['limit'];

        self::assertInstanceOf(FormInterface::class, $offsetForm);
        self::assertInstanceOf(FormInterface::class, $limitForm);

        self::assertSame('10', $offsetForm->getData());
        self::assertSame('5', $limitForm->getData());
    }

    public function testMapDataToFormsWithNoLimit(): void
    {
        $data = new CollectionUpdateStruct();
        $data->offset = 10;
        $data->limit = 0;

        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset'),
                'limit' => $this->getForm('limit'),
            ],
        );

        $this->mapper->mapDataToForms($data, $forms);

        $offsetForm = $forms['offset'];
        $limitForm = $forms['limit'];

        self::assertInstanceOf(FormInterface::class, $offsetForm);
        self::assertInstanceOf(FormInterface::class, $limitForm);

        self::assertSame('10', $offsetForm->getData());
        self::assertNull($limitForm->getData());
    }

    public function testMapFormsToData(): void
    {
        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset', 10),
                'limit' => $this->getForm('limit', 5),
            ],
        );

        $data = new CollectionUpdateStruct();

        $this->mapper->mapFormsToData($forms, $data);

        self::assertSame(10, $data->offset);
        self::assertSame(5, $data->limit);
    }

    public function testMapFormsToDataWithNoLimit(): void
    {
        $forms = new ArrayIterator(
            [
                'offset' => $this->getForm('offset', 10),
                'limit' => $this->getForm('limit'),
            ],
        );

        $data = new CollectionUpdateStruct();

        $this->mapper->mapFormsToData($forms, $data);

        self::assertSame(10, $data->offset);
        self::assertSame(0, $data->limit);
    }
}
