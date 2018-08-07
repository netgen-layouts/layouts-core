<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeGroupNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class BlockTypeGroupNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeGroupNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizer = new BlockTypeGroupNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeGroupNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $blockTypeGroup = BlockTypeGroup::fromArray(
            [
                'identifier' => 'identifier',
                'isEnabled' => true,
                'name' => 'Block group',
                'blockTypes' => [
                    BlockType::fromArray(['isEnabled' => false, 'identifier' => 'type1']),
                    BlockType::fromArray(['isEnabled' => true, 'identifier' => 'type2']),
                ],
            ]
        );

        self::assertSame(
            [
                'identifier' => $blockTypeGroup->getIdentifier(),
                'enabled' => true,
                'name' => $blockTypeGroup->getName(),
                'block_types' => ['type2'],
            ],
            $this->normalizer->normalize(new VersionedValue($blockTypeGroup, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeGroupNormalizer::supportsNormalization
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
            [new BlockTypeGroup(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new BlockTypeGroup(), 2), false],
            [new VersionedValue(new BlockTypeGroup(), 1), true],
        ];
    }
}
