<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Form\DataMapper;

use ArrayIterator;
use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper;

final class DateTimeDataMapperTest extends DataMapperTest
{
    /**
     * @var \Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new DateTimeDataMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper::__construct
     * @covers \Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper::mapDataToForms
     */
    public function testMapDataToForms(): void
    {
        $value = new DateTimeImmutable('2018-02-01 15:00:00.000000', new DateTimeZone('Antarctica/Casey'));

        $forms = new ArrayIterator(
            [
                'datetime' => $this->getForm('datetime'),
                'timezone' => $this->getForm('timezone'),
            ]
        );

        $this->mapper->mapDataToForms($value, $forms);

        $this->assertSame('2018-02-01 15:00:00', $forms['datetime']->getData());
        $this->assertSame('Antarctica/Casey', $forms['timezone']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper::mapDataToForms
     * @dataProvider mapDataToFormsWithArrayProvider
     */
    public function testMapDataToFormsWithArray(array $input, ?string $dateTime, string $timeZone): void
    {
        $forms = new ArrayIterator(
            [
                'datetime' => $this->getForm('datetime'),
                'timezone' => $this->getForm('timezone'),
            ]
        );

        $this->mapper->mapDataToForms($input, $forms);

        $this->assertSame($dateTime, $forms['datetime']->getData());
        $this->assertSame($timeZone, $forms['timezone']->getData());
    }

    public function mapDataToFormsWithArrayProvider(): array
    {
        return [
            [['datetime' => '2018-02-01 15:00:00', 'timezone' => 'Antarctica/Casey'], '2018-02-01 15:00:00', 'Antarctica/Casey'],
            [['datetime' => '2018-02-01 15:00:00', 'timezone' => null], '2018-02-01 15:00:00', date_default_timezone_get()],
            [['datetime' => '2018-02-01 15:00:00'], '2018-02-01 15:00:00', date_default_timezone_get()],
            [['datetime' => null, 'timezone' => 'Antarctica/Casey'], null, 'Antarctica/Casey'],
            [['timezone' => 'Antarctica/Casey'], null, 'Antarctica/Casey'],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithNoDateTime(): void
    {
        $forms = new ArrayIterator(
            [
                'datetime' => $this->getForm('datetime'),
                'timezone' => $this->getForm('timezone'),
            ]
        );

        $this->mapper->mapDataToForms(null, $forms);

        $this->assertNull($forms['datetime']->getData());
        $this->assertSame(date_default_timezone_get(), $forms['timezone']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper::mapFormsToData
     */
    public function testMapFormsToData(): void
    {
        $forms = new ArrayIterator(
            [
                'datetime' => $this->getForm('datetime', '2018-02-01 15:00:00'),
                'timezone' => $this->getForm('timezone', 'Antarctica/Casey'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertInstanceOf(DateTimeImmutable::class, $data);
        $this->assertSame('2018-02-01 15:00:00', $data->format('Y-m-d H:i:s'));
        $this->assertSame('Antarctica/Casey', $data->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper::mapFormsToData
     */
    public function testMapFormsToDataByUsingArray(): void
    {
        $this->mapper = new DateTimeDataMapper(false);

        $forms = new ArrayIterator(
            [
                'datetime' => $this->getForm('datetime', '2018-02-01 15:00:00'),
                'timezone' => $this->getForm('timezone', 'Antarctica/Casey'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertSame(
            [
                'datetime' => '2018-02-01 15:00:00',
                'timezone' => 'Antarctica/Casey',
            ],
            $data
        );
    }

    /**
     * @covers \Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithEmptyFormData(): void
    {
        $forms = new ArrayIterator(
            [
                'datetime' => $this->getForm('datetime', ''),
                'timezone' => $this->getForm('timezone', 'Antarctica/Casey'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertNull($data);
    }
}
