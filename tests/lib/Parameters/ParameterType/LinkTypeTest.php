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
use Netgen\Layouts\Parameters\Value\LinkValue;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

final class LinkTypeTest extends TestCase
{
    use ExportObjectTrait;
    use ParameterTypeTestTrait;

    private ValueTypeRegistry $valueTypeRegistry;

    private MockObject $cmsItemLoaderMock;

    protected function setUp(): void
    {
        $this->valueTypeRegistry = new ValueTypeRegistry(
            [
                'default' => ValueType::fromArray(['isEnabled' => true, 'supportsManualItems' => true]),
                'no_manual' => ValueType::fromArray(['isEnabled' => true, 'supportsManualItems' => false]),
                'disabled' => ValueType::fromArray(['isEnabled' => false, 'supportsManualItems' => true]),
            ],
        );

        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->type = new LinkType($this->valueTypeRegistry, new RemoteIdConverter($this->cmsItemLoaderMock));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::__construct
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('link', $this->type::getIdentifier());
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $resolvedOptions
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::configureOptions
     *
     * @dataProvider validOptionsDataProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @param mixed[] $options
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::configureOptions
     *
     * @dataProvider invalidOptionsDataProvider
     */
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
                ['value_types' => ['default'], 'allow_invalid_internal' => false],
            ],
            [
                ['value_types' => ['value']],
                ['value_types' => ['value'], 'allow_invalid_internal' => false],
            ],
            [
                ['allow_invalid_internal' => false],
                ['value_types' => ['default'], 'allow_invalid_internal' => false],
            ],
            [
                ['allow_invalid_internal' => true],
                ['value_types' => ['default'], 'allow_invalid_internal' => true],
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
                [
                    'value_types' => [42],
                ],
                [
                    'value_types' => ['disabled'],
                ],
                [
                    'allow_invalid_internal' => 1,
                ],
                [
                    'allow_invalid_internal' => 0,
                ],
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param string[] $valueTypes
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::getRequiredConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::getValueConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $isRequired, array $valueTypes, bool $isValid): void
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
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io', 'linkSuffix' => 'suffix']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io', 'newWindow' => true]), true, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io', 'newWindow' => false]), true, [], true],
            [LinkValue::fromArray(['linkType' => '', 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => '', 'link' => 'https://netgen.io']), true, [], false],
            [LinkValue::fromArray(['linkType' => '', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => '', 'link' => 'https://netgen.io']), false, [], false],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'invalid']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'invalid']), false, [], false],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'info@netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'info@netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'invalid']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'invalid']), false, [], false],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => 'info@netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => 'info@netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'default://42']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), false, [], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'default://42']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), false, [], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), true, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), true, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), true, ['value'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), false, ['value'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), true, ['other'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), false, ['other'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), false, ['other'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), false, ['other'], false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::toHash
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
            [
                42,
                null,
            ],
            [
                LinkValue::fromArray(
                    [
                        'linkType' => 'url',
                        'link' => 'https://netgen.io',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ],
                ),
                [
                    'link_type' => 'url',
                    'link' => 'https://netgen.io',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $value
     * @param array<string, mixed> $expectedValue
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::fromHash
     *
     * @dataProvider fromHashDataProvider
     */
    public function testFromHash($value, array $expectedValue): void
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
                    'linkType' => '',
                    'newWindow' => false,
                ],
            ],
            [
                [],
                [
                    'link' => '',
                    'linkSuffix' => '',
                    'linkType' => '',
                    'newWindow' => false,
                ],
            ],
            [
                [
                    'link_type' => 'url',
                    'link' => 'https://netgen.io',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'link' => 'https://netgen.io',
                    'linkSuffix' => '?suffix',
                    'linkType' => 'url',
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => 'url',
                    'link' => 'https://netgen.io',
                ],
                [
                    'link' => 'https://netgen.io',
                    'linkSuffix' => '',
                    'linkType' => 'url',
                    'newWindow' => false,
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::export
     *
     * @dataProvider exportDataProvider
     */
    public function testExport($value, $convertedValue): void
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

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::export
     */
    public function testExportWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->method('load')
            ->with(self::identicalTo('24'), self::identicalTo('my_value_type'))
            ->willReturn(new NullCmsItem('my_value_type'));

        self::assertSame(
            [
                'link_type' => 'internal',
                'link' => 'null://0',
                'link_suffix' => '?suffix',
                'new_window' => true,
            ],
            $this->type->export(
                $this->getParameterDefinition(),
                LinkValue::fromArray(
                    [
                        'linkType' => 'internal',
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
                        'linkType' => 'url',
                        'link' => 'https://netgen.io',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ],
                ),
                [
                    'link_type' => 'url',
                    'link' => 'https://netgen.io',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
            [
                LinkValue::fromArray(
                    [
                        'linkType' => 'internal',
                        'link' => 'my-value-type://42',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ],
                ),
                [
                    'link_type' => 'internal',
                    'link' => 'my-value-type://abc',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
            [
                LinkValue::fromArray(
                    [
                        'linkType' => 'internal',
                        'link' => 'invalid',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ],
                ),
                [
                    'link_type' => 'internal',
                    'link' => 'null://0',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param array<string, mixed>  $expectedValue
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::import
     *
     * @dataProvider importDataProvider
     */
    public function testImport($value, array $expectedValue): void
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

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::import
     */
    public function testImportWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->method('loadByRemoteId')
            ->with(self::identicalTo('def'), self::identicalTo('my_value_type'))
            ->willReturn(new NullCmsItem('my_value_type'));

        $importedValue = $this->type->import(
            $this->getParameterDefinition(),
            [
                'link_type' => 'internal',
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
                'linkType' => 'internal',
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
                    'linkType' => '',
                    'newWindow' => false,
                ],
            ],
            [
                [],
                [
                    'link' => '',
                    'linkSuffix' => '',
                    'linkType' => '',
                    'newWindow' => false,
                ],
            ],
            [
                [
                    'link_type' => 'url',
                    'link' => 'https://netgen.io',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'link' => 'https://netgen.io',
                    'linkSuffix' => '?suffix',
                    'linkType' => 'url',
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => 'url',
                    'link' => 'https://netgen.io',
                ],
                [
                    'link' => 'https://netgen.io',
                    'linkSuffix' => '',
                    'linkType' => 'url',
                    'newWindow' => false,
                ],
            ],
            [
                [
                    'link_type' => 'internal',
                    'link' => 'my-value-type://abc',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'link' => 'my-value-type://42',
                    'linkSuffix' => '?suffix',
                    'linkType' => 'internal',
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => 'internal',
                    'link' => 'invalid',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'link' => 'null://0',
                    'linkSuffix' => '?suffix',
                    'linkType' => 'internal',
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => 'internal',
                ],
                [
                    'link' => 'null://0',
                    'linkSuffix' => '',
                    'linkType' => 'internal',
                    'newWindow' => false,
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\LinkType::isValueEmpty
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
            [new LinkValue(), true],
            [LinkValue::fromArray(['linkType' => 'url']), true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io']), false],
            [LinkValue::fromArray(['linkType' => 'url', 'linkSuffix' => '?suffix']), false],
            [LinkValue::fromArray(['linkType' => 'email']), true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'info@netgen.io']), false],
            [LinkValue::fromArray(['linkType' => 'email', 'linkSuffix' => '?suffix']), true],
            [LinkValue::fromArray(['linkType' => 'tel']), true],
            [LinkValue::fromArray(['linkType' => 'tel', 'link' => '123456']), false],
            [LinkValue::fromArray(['linkType' => 'tel', 'linkSuffix' => '?suffix']), true],
            [LinkValue::fromArray(['linkType' => 'internal']), true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'my_value_type://42']), false],
            [LinkValue::fromArray(['linkType' => 'internal', 'linkSuffix' => '?suffix']), true],
        ];
    }
}
