<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\Layouts\Parameters\ParameterType\DateTimeType;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class DateTimeTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    protected function setUp(): void
    {
        $this->type = new DateTimeType();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\DateTimeType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('datetime', $this->type::getIdentifier());
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\DateTimeType::isValueEmpty
     *
     * @dataProvider emptyDataProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    public static function emptyDataProvider(): iterable
    {
        return [
            [null, true],
            [new DateTimeImmutable(), false],
            [new DateTimeImmutable(), false],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), false],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\DateTimeType::toHash
     *
     * @dataProvider toHashDataProvider
     */
    public function testToHash($value, $convertedValue): void
    {
        self::assertSame($convertedValue, $this->type->toHash($this->getParameterDefinition(), $value));
    }

    public static function toHashDataProvider(): iterable
    {
        return [
            [42, null],
            [null, null],
            [[], null],
            [['datetime' => '2018-02-01 00:00:00'], null],
            [['timezone' => 'Antarctica/Casey'], null],
            [['datetime' => '2018-02-01 00:00:00', 'timezone' => ''], null],
            [['datetime' => '', 'timezone' => 'Antarctica/Casey'], null],
            [['datetime' => '', 'timezone' => ''], null],
            [['datetime' => '2018-02-01 15:00:00', 'timezone' => 'Antarctica/Casey'], null],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), ['datetime' => '2018-02-01 15:00:00.000000', 'timezone' => 'Antarctica/Casey']],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), ['datetime' => '2018-02-01 15:00:00.000000', 'timezone' => 'Antarctica/Casey']],
        ];
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\DateTimeType::fromHash
     */
    public function testFromHash(): void
    {
        $convertedValue = $this->type->fromHash(
            $this->getParameterDefinition(),
            [
                'datetime' => '2018-02-01 15:00:00.000000',
                'timezone' => 'Antarctica/Casey',
            ],
        );

        self::assertInstanceOf(DateTimeImmutable::class, $convertedValue);
        self::assertSame('2018-02-01 15:00:00', $convertedValue->format('Y-m-d H:i:s'));
        self::assertSame('Antarctica/Casey', $convertedValue->getTimezone()->getName());
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\DateTimeType::fromHash
     *
     * @dataProvider invalidFromHashDataProvider
     */
    public function testFromHashWithInvalidValues($value, $convertedValue): void
    {
        self::assertSame($convertedValue, $this->type->fromHash($this->getParameterDefinition(), $value));
    }

    public static function invalidFromHashDataProvider(): iterable
    {
        return [
            [null, null],
            [[], null],
            [['datetime' => '2018-02-01 00:00:00'], null],
            [['timezone' => 'Antarctica/Casey'], null],
            [['datetime' => '2018-02-01 00:00:00', 'timezone' => ''], null],
            [['datetime' => '', 'timezone' => 'Antarctica/Casey'], null],
            [['datetime' => '', 'timezone' => ''], null],
        ];
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\DateTimeType::getRequiredConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType\DateTimeType::getValueConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition();
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public static function validationDataProvider(): iterable
    {
        return [
            [null, true],
            [new DateTimeImmutable(), true],
            [new DateTimeImmutable(), true],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), true],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), true],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('+01:00')), false],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('+01:00')), false],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('CAST')), false],
            [new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('CAST')), false],
        ];
    }
}
