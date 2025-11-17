<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Parameters\ParameterType\LinkType;
use Netgen\Layouts\Parameters\Value\LinkType as LinkTypeEnum;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

#[CoversClass(RemoteIdConverter::class)]
#[CoversClass(LinkType::class)]
final class LinkTypeTest extends TestCase
{
    use ExportObjectTrait;
    use ParameterTypeTestTrait;

    private MockObject&CmsItemLoaderInterface $cmsItemLoaderMock;

    protected function setUp(): void
    {
        $valueTypeRegistry = new ValueTypeRegistry(
            [
                'default' => ValueType::fromArray(['isEnabled' => true, 'supportsManualItems' => true]),
                'no_manual' => ValueType::fromArray(['isEnabled' => true, 'supportsManualItems' => false]),
                'disabled' => ValueType::fromArray(['isEnabled' => false, 'supportsManualItems' => true]),
            ],
        );

        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->type = new LinkType($valueTypeRegistry, new RemoteIdConverter($this->cmsItemLoaderMock));
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('link', $this->type::getIdentifier());
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
                ['allow_invalid_internal' => false, 'value_types' => ['default']],
            ],
            [
                ['value_types' => ['value']],
                ['allow_invalid_internal' => false, 'value_types' => ['value']],
            ],
            [
                ['value_types' => ['disabled']],
                ['allow_invalid_internal' => false, 'value_types' => ['disabled']],
            ],
            [
                ['allow_invalid_internal' => false],
                ['allow_invalid_internal' => false, 'value_types' => ['default']],
            ],
            [
                ['allow_invalid_internal' => true],
                ['allow_invalid_internal' => true, 'value_types' => ['default']],
            ],
        ];
    }

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
                    'allow_invalid_internal' => 1,
                ],
            ],
            [
                [
                    'allow_invalid_internal' => 0,
                ],
            ],
            [
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param string[] $valueTypes
     */
    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $value, bool $isRequired, array $valueTypes, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition(['required' => $isRequired, 'value_types' => $valueTypes]);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public static function validationDataProvider(): iterable
    {
        return [
            [null, true, [], true],
            [null, false, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => 'https://netgen.io', 'linkSuffix' => 'suffix']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => 'https://netgen.io', 'newWindow' => true]), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => 'https://netgen.io', 'newWindow' => false]), true, [], true],
            [LinkValue::fromArray(['linkType' => null, 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => null, 'link' => 'https://netgen.io']), true, [], false],
            [LinkValue::fromArray(['linkType' => null, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => null, 'link' => 'https://netgen.io']), false, [], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => 'https://netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => 'https://netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => 'invalid']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => 'invalid']), false, [], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Email, 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Email, 'link' => 'info@netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Email, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Email, 'link' => 'info@netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Email, 'link' => 'invalid']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Email, 'link' => 'invalid']), false, [], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Phone, 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Phone, 'link' => 'info@netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Phone, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Phone, 'link' => 'info@netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value://42']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'default://42']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value://42']), false, [], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'default://42']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value']), false, [], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => '']), true, ['value'], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value://42']), true, ['value'], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => '']), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value://42']), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value']), true, ['value'], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value']), false, ['value'], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => '']), true, ['other'], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value://42']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => '']), false, ['other'], true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value://42']), false, ['other'], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'value']), false, ['other'], false],
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
            [
                42,
                null,
            ],
            [
                LinkValue::fromArray(
                    [
                        'linkType' => LinkTypeEnum::Url,
                        'link' => 'https://netgen.io',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ],
                ),
                [
                    'link_type' => LinkTypeEnum::Url->value,
                    'link' => 'https://netgen.io',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expectedValue
     */
    #[DataProvider('fromHashDataProvider')]
    public function testFromHash(mixed $value, array $expectedValue): void
    {
        $convertedValue = $this->type->fromHash($this->getParameterDefinition(), $value);

        self::assertInstanceOf(LinkValue::class, $convertedValue);
        self::assertSame($expectedValue, $this->exportObject($convertedValue));
    }

    public static function fromHashDataProvider(): iterable
    {
        return [
            [
                42,
                [
                    'link' => '',
                    'linkSuffix' => '',
                    'linkType' => null,
                    'newWindow' => false,
                ],
            ],
            [
                [],
                [
                    'link' => '',
                    'linkSuffix' => '',
                    'linkType' => null,
                    'newWindow' => false,
                ],
            ],
            [
                [
                    'link_type' => LinkTypeEnum::Url->value,
                    'link' => 'https://netgen.io',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'link' => 'https://netgen.io',
                    'linkSuffix' => '?suffix',
                    'linkType' => LinkTypeEnum::Url,
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => LinkTypeEnum::Url->value,
                    'link' => 'https://netgen.io',
                ],
                [
                    'link' => 'https://netgen.io',
                    'linkSuffix' => '',
                    'linkType' => LinkTypeEnum::Url,
                    'newWindow' => false,
                ],
            ],
        ];
    }

    #[DataProvider('exportDataProvider')]
    public function testExport(mixed $value, mixed $convertedValue): void
    {
        $this->cmsItemLoaderMock
            ->method('load')
            ->with(self::identicalTo('42'), self::identicalTo('my_value_type'))
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ],
                ),
            );

        self::assertSame($convertedValue, $this->type->export($this->getParameterDefinition(), $value));
    }

    public function testExportWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->method('load')
            ->with(self::identicalTo('24'), self::identicalTo('my_value_type'))
            ->willReturn(new NullCmsItem('my_value_type'));

        self::assertSame(
            [
                'link_type' => LinkTypeEnum::Internal->value,
                'link' => 'null://0',
                'link_suffix' => '?suffix',
                'new_window' => true,
            ],
            $this->type->export(
                $this->getParameterDefinition(),
                LinkValue::fromArray(
                    [
                        'linkType' => LinkTypeEnum::Internal,
                        'link' => 'my_value_type://24',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ],
                ),
            ),
        );
    }

    public static function exportDataProvider(): iterable
    {
        return [
            [
                42,
                null,
            ],
            [
                LinkValue::fromArray(
                    [
                        'linkType' => LinkTypeEnum::Url,
                        'link' => 'https://netgen.io',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ],
                ),
                [
                    'link_type' => LinkTypeEnum::Url->value,
                    'link' => 'https://netgen.io',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
            [
                LinkValue::fromArray(
                    [
                        'linkType' => LinkTypeEnum::Internal,
                        'link' => 'my-value-type://42',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ],
                ),
                [
                    'link_type' => LinkTypeEnum::Internal->value,
                    'link' => 'my-value-type://abc',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
            [
                LinkValue::fromArray(
                    [
                        'linkType' => LinkTypeEnum::Internal,
                        'link' => 'invalid',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ],
                ),
                [
                    'link_type' => LinkTypeEnum::Internal->value,
                    'link' => 'null://0',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed>  $expectedValue
     */
    #[DataProvider('importDataProvider')]
    public function testImport(mixed $value, array $expectedValue): void
    {
        $this->cmsItemLoaderMock
            ->method('loadByRemoteId')
            ->with(self::identicalTo('abc'), self::identicalTo('my_value_type'))
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ],
                ),
            );

        $convertedValue = $this->type->import($this->getParameterDefinition(), $value);

        self::assertInstanceOf(LinkValue::class, $convertedValue);
        self::assertSame($expectedValue, $this->exportObject($convertedValue));
    }

    public function testImportWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->method('loadByRemoteId')
            ->with(self::identicalTo('def'), self::identicalTo('my_value_type'))
            ->willReturn(new NullCmsItem('my_value_type'));

        $importedValue = $this->type->import(
            $this->getParameterDefinition(),
            [
                'link_type' => LinkTypeEnum::Internal->value,
                'link' => 'my_value_type://def',
                'link_suffix' => '?suffix',
                'new_window' => true,
            ],
        );

        self::assertInstanceOf(LinkValue::class, $importedValue);

        self::assertSame(
            [
                'link' => 'null://0',
                'linkSuffix' => '?suffix',
                'linkType' => LinkTypeEnum::Internal,
                'newWindow' => true,
            ],
            $this->exportObject($importedValue),
        );
    }

    public static function importDataProvider(): iterable
    {
        return [
            [
                42,
                [
                    'link' => '',
                    'linkSuffix' => '',
                    'linkType' => null,
                    'newWindow' => false,
                ],
            ],
            [
                [],
                [
                    'link' => '',
                    'linkSuffix' => '',
                    'linkType' => null,
                    'newWindow' => false,
                ],
            ],
            [
                [
                    'link_type' => LinkTypeEnum::Url->value,
                    'link' => 'https://netgen.io',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'link' => 'https://netgen.io',
                    'linkSuffix' => '?suffix',
                    'linkType' => LinkTypeEnum::Url,
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => LinkTypeEnum::Url->value,
                    'link' => 'https://netgen.io',
                ],
                [
                    'link' => 'https://netgen.io',
                    'linkSuffix' => '',
                    'linkType' => LinkTypeEnum::Url,
                    'newWindow' => false,
                ],
            ],
            [
                [
                    'link_type' => LinkTypeEnum::Internal->value,
                    'link' => 'my-value-type://abc',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'link' => 'my-value-type://42',
                    'linkSuffix' => '?suffix',
                    'linkType' => LinkTypeEnum::Internal,
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => LinkTypeEnum::Internal->value,
                    'link' => 'invalid',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'link' => 'null://0',
                    'linkSuffix' => '?suffix',
                    'linkType' => LinkTypeEnum::Internal,
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => LinkTypeEnum::Internal->value,
                ],
                [
                    'link' => 'null://0',
                    'linkSuffix' => '',
                    'linkType' => LinkTypeEnum::Internal,
                    'newWindow' => false,
                ],
            ],
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
            [new LinkValue(), true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url]), true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'link' => 'https://netgen.io']), false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Url, 'linkSuffix' => '?suffix']), false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Email]), true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Email, 'link' => 'info@netgen.io']), false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Email, 'linkSuffix' => '?suffix']), true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Phone]), true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Phone, 'link' => '123456']), false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Phone, 'linkSuffix' => '?suffix']), true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal]), true],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'link' => 'my_value_type://42']), false],
            [LinkValue::fromArray(['linkType' => LinkTypeEnum::Internal, 'linkSuffix' => '?suffix']), true],
        ];
    }
}
