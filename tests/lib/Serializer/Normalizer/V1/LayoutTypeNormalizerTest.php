<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\LayoutTypeFactory;
use Netgen\BlockManager\Layout\Type\Zone;
use Netgen\BlockManager\Serializer\Normalizer\V1\LayoutTypeNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class LayoutTypeNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutTypeNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizer = new LayoutTypeNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutTypeNormalizer::getZones
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutTypeNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $layoutType = LayoutTypeFactory::buildLayoutType(
            '4_zones_a',
            [
                'name' => 'Layout type',
                'icon' => '/icon.svg',
                'enabled' => true,
                'zones' => [
                    'zone1' => [
                        'name' => 'Zone 1',
                        'allowed_block_definitions' => ['title'],
                    ],
                    'zone2' => [
                        'name' => 'Zone 2',
                        'allowed_block_definitions' => [],
                    ],
                ],
            ]
        );

        $this->assertEquals(
            [
                'identifier' => $layoutType->getIdentifier(),
                'name' => $layoutType->getName(),
                'icon' => $layoutType->getIcon(),
                'zones' => array_map(
                    function (Zone $zone): array {
                        return [
                            'identifier' => $zone->getIdentifier(),
                            'name' => $zone->getName(),
                            'allowed_block_definitions' => !empty($zone->getAllowedBlockDefinitions()) ?
                                $zone->getAllowedBlockDefinitions() :
                                true,
                        ];
                    },
                    array_values($layoutType->getZones())
                ),
            ],
            $this->normalizer->normalize(new VersionedValue($layoutType, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutTypeNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        $this->assertEquals($expected, $this->normalizer->supportsNormalization($data));
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
            [new LayoutType(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new LayoutType(), 2), false],
            [new VersionedValue(new LayoutType(), 1), true],
        ];
    }
}
