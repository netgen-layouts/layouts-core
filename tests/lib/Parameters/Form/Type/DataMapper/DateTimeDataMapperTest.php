<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Type\DataMapper;

use ArrayIterator;
use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\DateTimeDataMapper;

final class DateTimeDataMapperTest extends DataMapperTest
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Type\DataMapper\DateTimeDataMapper
     */
    private $mapper;

    public function setUp()
    {
        parent::setUp();

        $this->mapper = new DateTimeDataMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\DateTimeDataMapper::mapDataToForms
     */
    public function testMapDataToForms()
    {
        $value = new DateTimeImmutable('2018-02-01 15:00:00.000000', new DateTimeZone('Antarctica/Casey'));

        $forms = new ArrayIterator(
            array(
                'datetime' => $this->getForm('datetime'),
                'timezone' => $this->getForm('timezone'),
            )
        );

        $this->mapper->mapDataToForms($value, $forms);

        $this->assertEquals('2018-02-01 15:00:00', $forms['datetime']->getData());
        $this->assertEquals('Antarctica/Casey', $forms['timezone']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\DateTimeDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithNoDateTime()
    {
        $forms = new ArrayIterator(
            array(
                'datetime' => $this->getForm('datetime'),
                'timezone' => $this->getForm('timezone'),
            )
        );

        $this->mapper->mapDataToForms(null, $forms);

        $this->assertNull($forms['datetime']->getData());
        $this->assertEquals(date_default_timezone_get(), $forms['timezone']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\DateTimeDataMapper::mapFormsToData
     */
    public function testMapFormsToData()
    {
        $forms = new ArrayIterator(
            array(
                'datetime' => $this->getForm('datetime', '2018-02-01 15:00:00'),
                'timezone' => $this->getForm('timezone', 'Antarctica/Casey'),
            )
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertEquals(
            array(
                'datetime' => '2018-02-01 15:00:00',
                'timezone' => 'Antarctica/Casey',
            ),
            $data
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\DateTimeDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithEmptyFormData()
    {
        $forms = new ArrayIterator(
            array(
                'datetime' => $this->getForm('datetime', ''),
                'timezone' => $this->getForm('timezone', 'Antarctica/Casey'),
            )
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertNull($data);
    }
}
