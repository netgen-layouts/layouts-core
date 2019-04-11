<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Normalizer\V1;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Serializer\Normalizer\V1\BlockTypeNormalizer;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Block\Stubs\ContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class BlockTypeNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Serializer\Normalizer\V1\BlockTypeNormalizer
     */
    private $normalizer;

    /**
     * @var \Netgen\Layouts\Block\BlockDefinitionInterface
     */
    private $blockDefinition;

    public function setUp(): void
    {
        $this->blockDefinition = BlockDefinition::fromArray(['identifier' => 'title']);

        $this->normalizer = new BlockTypeNormalizer();
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\BlockTypeNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $blockType = BlockType::fromArray(
            [
                'identifier' => 'identifier',
                'icon' => '/icon.svg',
                'isEnabled' => false,
                'name' => 'Block type',
                'definition' => $this->blockDefinition,
                'defaults' => [
                    'name' => 'Default name',
                    'view_type' => 'Default view type',
                    'parameters' => ['param' => 'value'],
                ],
            ]
        );

        self::assertSame(
            [
                'identifier' => $blockType->getIdentifier(),
                'enabled' => false,
                'name' => $blockType->getName(),
                'icon' => $blockType->getIcon(),
                'definition_identifier' => $this->blockDefinition->getIdentifier(),
                'is_container' => false,
                'defaults' => $blockType->getDefaults(),
            ],
            $this->normalizer->normalize(new VersionedValue($blockType, 1))
        );
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\BlockTypeNormalizer::normalize
     */
    public function testNormalizeWithContainerBlock(): void
    {
        $blockType = BlockType::fromArray(
            [
                'identifier' => 'definition',
                'name' => 'Block type',
                'isEnabled' => true,
                'definition' => ContainerDefinition::fromArray(
                    [
                        'identifier' => 'definition',
                        'handler' => new ContainerDefinitionHandler(),
                    ]
                ),
            ]
        );

        $data = $this->normalizer->normalize(new VersionedValue($blockType, 1));

        self::assertIsArray($data);
        self::assertTrue($data['is_container']);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\BlockTypeNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public function supportsNormalizationProvider(): array
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new BlockType(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new BlockType(), 2), false],
            [new VersionedValue(new BlockType(), 1), true],
        ];
    }
}
