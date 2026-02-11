<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Parameters\ParameterType\ItemLinkType;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

#[CoversClass(ItemLinkType::class)]
final class ItemLinkTypeTest extends TestCase
{
    use ParameterTypeTestTrait;
    use ValidatorTestCaseTrait;

    protected function setUp(): void
    {
        $valueTypeRegistry = new ValueTypeRegistry(
            [
                'default' => ValueType::fromArray(['isEnabled' => true, 'supportsManualItems' => true]),
                'no_manual' => ValueType::fromArray(['isEnabled' => true, 'supportsManualItems' => false]),
                'disabled' => ValueType::fromArray(['isEnabled' => false, 'supportsManualItems' => true]),
            ],
        );

        $cmsItemLoaderStub = self::createStub(CmsItemLoaderInterface::class);
        $cmsItemLoaderStub
            ->method('load')
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ],
                ),
            );

        $cmsItemLoaderStub
            ->method('loadByRemoteId')
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ],
                ),
            );

        $this->type = new ItemLinkType($valueTypeRegistry, new RemoteIdConverter($cmsItemLoaderStub));
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('item_link', $this->type::getIdentifier());
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $resolvedOptions
     */
    #[DataProvider('validOptionsDataProvider')]
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameterDefinition = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameterDefinition->options);
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

    /**
     * @return iterable<mixed>
     */
    public static function validOptionsDataProvider(): iterable
    {
        return [
            [
                [],
                ['value_types' => ['default'], 'allow_invalid' => false],
            ],
            [
                ['value_types' => ['value']],
                ['value_types' => ['value'], 'allow_invalid' => false],
            ],
            [
                ['value_types' => ['disabled']],
                ['value_types' => ['disabled'], 'allow_invalid' => false],
            ],
            [
                ['allow_invalid' => false],
                ['value_types' => ['default'], 'allow_invalid' => false],
            ],
            [
                ['allow_invalid' => true],
                ['value_types' => ['default'], 'allow_invalid' => true],
            ],
        ];
    }

    /**
     * @return iterable<mixed>
     */
    public static function invalidOptionsDataProvider(): iterable
    {
        return [
            [
                [
                    'value_types' => 42,
                ],
            ],
            [
                [
                    'value_types' => [42],
                ],
            ],
            [
                [
                    'allow_invalid' => 0,
                ],
            ],
            [
                [
                    'allow_invalid' => 1,
                ],
            ],
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
        $validator = $this->createValidator();

        $parameterDefinition = $this->getParameterDefinition();

        $errors = $validator->validate($value, $this->type->getConstraints($parameterDefinition, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @return iterable<mixed>
     */
    public static function validationDataProvider(): iterable
    {
        return [
            [null, true],
            ['42', false],
            ['value://42', false],
            ['default://42', true],
        ];
    }

    public function testExport(): void
    {
        self::assertSame('test-value-type://abc', $this->type->export($this->getParameterDefinition(), 'test-value-type://42'));
    }

    public function testExportWithInvalidValue(): void
    {
        self::assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    public function testImport(): void
    {
        self::assertSame('test-value-type://42', $this->type->import($this->getParameterDefinition(), 'test-value-type://abc'));
    }

    public function testImportWithInvalidValue(): void
    {
        self::assertNull($this->type->import($this->getParameterDefinition(), 42));
    }

    #[DataProvider('emptyDataProvider')]
    public function testIsValueEmpty(mixed $value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    /**
     * @return iterable<mixed>
     */
    public static function emptyDataProvider(): iterable
    {
        return [
            [null, true],
            ['', true],
            ['value', true],
            ['value:', true],
            ['value:/', true],
            ['value://', true],
            ['value://null', false],
            ['value://42', false],
            ['value://0', false],
        ];
    }
}
