<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Form\DataMapper;

use ArrayIterator;
use DateTimeImmutable;
use DateTimeZone;
use Netgen\Layouts\Form\DataMapper\DateTimeDataMapper;
use Symfony\Component\Form\FormInterface;

use function date_default_timezone_get;

final class DateTimeDataMapperTest extends DataMapperTestBase
{
    private DateTimeDataMapper $mapper;

    protected function setUp(): void
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
            ],
        );

        $this->mapper->mapDataToForms($value, $forms);

        $dateTimeForm = $forms['datetime'];
        $timeZoneForm = $forms['timezone'];

        self::assertInstanceOf(FormInterface::class, $dateTimeForm);
        self::assertInstanceOf(FormInterface::class, $timeZoneForm);

        self::assertSame('2018-02-01 15:00:00', $dateTimeForm->getData());
        self::assertSame('Antarctica/Casey', $timeZoneForm->getData());
    }

    /**
     * @param mixed[] $input
     *
     * @covers \Netgen\Layouts\Form\DataMapper\DateTimeDataMapper::mapDataToForms
     *
     * @dataProvider mapDataToFormsWithArrayDataProvider
     */
    public function testMapDataToFormsWithArray(array $input, ?string $dateTime, string $timeZone): void
    {
        $forms = new ArrayIterator(
            [
                'datetime' => $this->getForm('datetime'),
                'timezone' => $this->getForm('timezone'),
            ],
        );

        $this->mapper->mapDataToForms($input, $forms);

        $dateTimeForm = $forms['datetime'];
        $timeZoneForm = $forms['timezone'];

        self::assertInstanceOf(FormInterface::class, $dateTimeForm);
        self::assertInstanceOf(FormInterface::class, $timeZoneForm);

        self::assertSame($dateTime, $dateTimeForm->getData());
        self::assertSame($timeZone, $timeZoneForm->getData());
    }

    public static function mapDataToFormsWithArrayDataProvider(): iterable
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
            ],
        );

        $this->mapper->mapDataToForms(null, $forms);

        $dateTimeForm = $forms['datetime'];
        $timeZoneForm = $forms['timezone'];

        self::assertInstanceOf(FormInterface::class, $dateTimeForm);
        self::assertInstanceOf(FormInterface::class, $timeZoneForm);

        self::assertNull($dateTimeForm->getData());
        self::assertSame(date_default_timezone_get(), $timeZoneForm->getData());
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
            ],
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
            ],
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertSame(
            [
                'datetime' => '2018-02-01 15:00:00',
                'timezone' => 'Antarctica/Casey',
            ],
            $data,
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
            ],
        );

        $this->mapper->mapFormsToData($forms, $data);

        self::assertNull($data);
    }
}
