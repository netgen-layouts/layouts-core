<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Parameters\ParameterType\ItemLinkType;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

final class ItemLinkTypeTest extends TestCase
{
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

        $this->type = new ItemLinkType($this->valueTypeRegistry, new RemoteIdConverter($this->cmsItemLoaderMock));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::__construct
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('item_link', $this->type::getIdentifier());
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $resolvedOptions
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::configureOptions
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
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::configureOptions
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
                ['value_types' => ['default'], 'allow_invalid' => false],
            ],
            [
                ['value_types' => ['value']],
                ['value_types' => ['value'], 'allow_invalid' => false],
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
                    'allow_invalid' => 0,
                ],
                [
                    'allow_invalid' => 1,
                ],
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::getRequiredConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::getValueConstraints
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
            ['42', false],
            ['value://42', false],
            ['default://42', true],
        ];
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::export
     */
    public function testExport(): void
    {
        self::assertSame('my-value-type://abc', $this->type->export($this->getParameterDefinition(), 'my-value-type://42'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::export
     */
    public function testExportWithInvalidValue(): void
    {
        self::assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::import
     */
    public function testImport(): void
    {
        self::assertSame('my-value-type://42', $this->type->import($this->getParameterDefinition(), 'my-value-type://abc'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::import
     */
    public function testImportWithInvalidValue(): void
    {
        self::assertNull($this->type->import($this->getParameterDefinition(), 42));
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\ItemLinkType::isValueEmpty
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
