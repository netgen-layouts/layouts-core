<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Form\DataMapper;

use ArrayIterator;
use DateTimeImmutable;
use DateTimeZone;
use Netgen\Layouts\Form\DataMapper\DateTimeDataMapper;

final class DateTimeDataMapperTest extends DataMapperTest
{
    /**
     * @var \Netgen\Layouts\Form\DataMapper\DateTimeDataMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new DateTimeDataMapper();
    }

    /**
     * @covers \Netgen\Layouts\Form\DataMapper\DateTimeDataMapper::__construct
     * @covers \Netgen\Layouts\Form\DataMapper\DateTimeDataMapper::mapDataToForms
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

        self::assertSame('2018-02-01 15:00:00', $forms['datetime']->getData());
        self::assertSame('Antarctica/Casey', $forms['timezone']->getData());
    }

    /**
     * @covers \Netgen\Layouts\Form\DataMapper\DateTimeDataMapper::mapDataToForms
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

        self::assertSame($dateTime, $forms['datetime']->getData());
        self::assertSame($timeZone, $forms['timezone']->getData());
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
     * @covers \Netgen\Layouts\Form\DataMapper\DateTimeDataMapper::mapDataToForms
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

        self::assertNull($forms['datetime']->getData());
        self::assertSame(date_default_timezone_get(), $forms['timezone']->getData());
    }

    /**
     * @covers \Netgen\Layouts\Form\DataMapper\DateTimeDataMapper::mapFormsToData
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

        self::assertInstanceOf(DateTimeImmutable::class, $data);
        self::assertSame('2018-02-01 15:00:00', $data->format('Y-m-d H:i:s'));
        self::assertSame('Antarctica/Casey', $data->getTimezone()->getName());
    }

    /**
     * @covers \Netgen\Layouts\Form\DataMapper\DateTimeDataMapper::mapFormsToData
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

        self::assertSame(
            [
                'datetime' => '2018-02-01 15:00:00',
                'timezone' => 'Antarctica/Casey',
            ],
            $data
        );
    }

    /**
     * @covers \Netgen\Layouts\Form\DataMapper\DateTimeDataMapper::mapFormsToData
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

        self::assertNull($data);
    }
}
