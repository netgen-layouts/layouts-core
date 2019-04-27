<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterType\EmailType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

final class EmailTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    protected function setUp(): void
    {
        $this->type = new EmailType();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\EmailType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('email', $this->type::getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\EmailType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\EmailType::configureOptions
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getParameterDefinition($options);
    }

    public function validOptionsProvider(): array
    {
        return [
            [
                [],
                [],
            ],
        ];
    }

    public function invalidOptionsProvider(): array
    {
        return [
            [
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\EmailType::getRequiredConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType\EmailType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition();
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public function validationProvider(): array
    {
        return [
            [null, true],
            ['info', false],
            ['info@example.com', true],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\EmailType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    public function emptyProvider(): array
    {
        return [
            [null, true],
            ['info@example.com', false],
            ['', true],
        ];
    }
}
