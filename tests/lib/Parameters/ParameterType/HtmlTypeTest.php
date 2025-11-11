<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterType\HtmlType;
use Netgen\Layouts\Utils\HtmlPurifier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

#[CoversClass(HtmlType::class)]
final class HtmlTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    protected function setUp(): void
    {
        $this->type = new HtmlType(new HtmlPurifier());
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('html', $this->type::getIdentifier());
    }

    public function testToHash(): void
    {
        $unsafeHtml = "<h1>Title</h1><script src=\"https://cool-hacker.com/cool-hacking-script.js\"></script><a onclick=\"alert('Haw-haw!');\" href=\"https://netgen.io\">Netgen</a>";
        $safeHtml = '<h1>Title</h1><a href="https://netgen.io">Netgen</a>';

        self::assertSame($safeHtml, $this->type->toHash($this->getParameterDefinition(), $unsafeHtml));
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $resolvedOptions
     */
    #[DataProvider('validOptionsDataProvider')]
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @param mixed[] $options
     */
    #[DataProvider('invalidOptionsDataProvider')]
    public function testInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getParameterDefinition($options);
    }

    public static function validOptionsDataProvider(): iterable
    {
        return [
            [
                [],
                [],
            ],
        ];
    }

    public static function invalidOptionsDataProvider(): iterable
    {
        return [
            [
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $value, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition();
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public static function validationDataProvider(): iterable
    {
        return [
            ['test', true],
            [null, true],
            [12.3, false],
            [12, false],
            [true, false],
            [false, false],
            [[], false],
        ];
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
            ['foo', false],
            ['', true],
        ];
    }
}
