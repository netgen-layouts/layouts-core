<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Parameters\ParameterType\DateTimeType;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

#[CoversClass(DateTimeType::class)]
final class DateTimeTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    protected function setUp(): void
    {
        $this->type = new DateTimeType();
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('datetime', $this->type::getIdentifier());
    }

    #[DataProvider('emptyDataProvider')]
    public function testIsValueEmpty(mixed $value, bool $isEmpty): void
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

    #[DataProvider('toHashDataProvider')]
    public function testToHash(mixed $value, mixed $convertedValue): void
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

    #[DataProvider('invalidFromHashDataProvider')]
    public function testFromHashWithInvalidValues(mixed $value, mixed $convertedValue): void
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

    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $value, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition();
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(
                new ValidatorFactory(
                    self::createStub(LayoutService::class),
                    self::createStub(LayoutResolverService::class),
                    self::createStub(CmsItemLoaderInterface::class),
                ),
            )
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
