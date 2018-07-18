<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\ItemLinkType;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ItemLinkTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    private $valueTypeRegistry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemLoaderMock;

    public function setUp(): void
    {
        $this->valueTypeRegistry = new ValueTypeRegistry(
            [
                'default' => ValueType::fromArray(['isEnabled' => true]),
                'disabled' => ValueType::fromArray(['isEnabled' => false]),
            ]
        );

        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);
        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->identicalTo('42'), $this->identicalTo('my_value_type'))
            ->will(
                $this->returnValue(
                    CmsItem::fromArray(
                        [
                            'value' => 42,
                            'remoteId' => 'abc',
                        ]
                    )
                )
            );

        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->with($this->identicalTo('abc'), $this->identicalTo('my_value_type'))
            ->will(
                $this->returnValue(
                    CmsItem::fromArray(
                        [
                            'value' => 42,
                            'remoteId' => 'abc',
                        ]
                    )
                )
            );

        $this->type = new ItemLinkType($this->valueTypeRegistry, new RemoteIdConverter($this->cmsItemLoaderMock));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('item_link', $this->type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        $this->assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->getParameterDefinition($options);
    }

    public function validOptionsProvider(): array
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

    public function invalidOptionsProvider(): array
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
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition();
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertSame($isValid, $errors->count() === 0);
    }

    public function validationProvider(): array
    {
        return [
            [null, true],
            ['42', false],
            ['value://42', false],
            ['default://42', true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::export
     */
    public function testExport(): void
    {
        $this->assertSame('my-value-type://abc', $this->type->export($this->getParameterDefinition(), 'my-value-type://42'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::export
     */
    public function testExportWithInvalidValue(): void
    {
        $this->assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::import
     */
    public function testImport(): void
    {
        $this->assertSame('my-value-type://42', $this->type->import($this->getParameterDefinition(), 'my-value-type://abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::import
     */
    public function testImportWithInvalidValue(): void
    {
        $this->assertNull($this->type->import($this->getParameterDefinition(), 42));
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLinkType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        $this->assertSame($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    public function emptyProvider(): array
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
