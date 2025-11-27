<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockTypeNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use Netgen\Layouts\Tests\Block\Stubs\ContainerDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockTypeNormalizer::class)]
final class BlockTypeNormalizerTest extends TestCase
{
    private BlockTypeNormalizer $normalizer;

    private BlockDefinition $blockDefinition;

    protected function setUp(): void
    {
        $this->blockDefinition = BlockDefinition::fromArray(['identifier' => 'title']);

        $this->normalizer = new BlockTypeNormalizer();
    }

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
            ],
        );

        self::assertSame(
            [
                'identifier' => $blockType->identifier,
                'enabled' => false,
                'name' => $blockType->name,
                'icon' => $blockType->icon,
                'definition_identifier' => $this->blockDefinition->identifier,
                'is_container' => false,
                'defaults' => $blockType->defaults,
            ],
            $this->normalizer->normalize(new Value($blockType)),
        );
    }

    public function testNormalizeWithContainerBlock(): void
    {
        $blockType = BlockType::fromArray(
            [
                'identifier' => 'definition',
                'name' => 'Block type',
                'icon' => null,
                'isEnabled' => true,
                'definition' => ContainerDefinition::fromArray(
                    [
                        'identifier' => 'definition',
                        'handler' => new ContainerDefinitionHandler(),
                    ],
                ),
            ],
        );

        $data = $this->normalizer->normalize(new Value($blockType));

        self::assertTrue($data['is_container']);
    }

    #[DataProvider('supportsNormalizationDataProvider')]
    public function testSupportsNormalization(mixed $data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public static function supportsNormalizationDataProvider(): iterable
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new BlockType(), false],
            [new Value(new APIValue()), false],
            [new Value(new BlockType()), true],
        ];
    }
}
