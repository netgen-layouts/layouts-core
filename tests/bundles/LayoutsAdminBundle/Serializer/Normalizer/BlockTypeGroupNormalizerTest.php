<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockTypeGroupNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockTypeGroupNormalizer::class)]
final class BlockTypeGroupNormalizerTest extends TestCase
{
    private BlockTypeGroupNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new BlockTypeGroupNormalizer();
    }

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
            ],
        );

        self::assertSame(
            [
                'identifier' => $blockTypeGroup->identifier,
                'enabled' => true,
                'name' => $blockTypeGroup->name,
                'block_types' => ['type2'],
            ],
            $this->normalizer->normalize(new Value($blockTypeGroup)),
        );
    }

    #[DataProvider('supportsNormalizationDataProvider')]
    public function testSupportsNormalization(mixed $data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    /**
     * @return iterable<mixed>
     */
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
            [new BlockTypeGroup(), false],
            [new Value(new APIValue()), false],
            [new Value(new BlockTypeGroup()), true],
        ];
    }
}
