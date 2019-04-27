<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Normalizer\V1;

use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Layout\Type\LayoutTypeFactory;
use Netgen\Layouts\Serializer\Normalizer\V1\LayoutTypeNormalizer;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

final class LayoutTypeNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Serializer\Normalizer\V1\LayoutTypeNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new LayoutTypeNormalizer();
        $this->normalizer->setNormalizer(new Serializer());
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\LayoutTypeNormalizer::getZones
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\LayoutTypeNormalizer::normalize
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

        self::assertSame(
            [
                'identifier' => '4_zones_a',
                'name' => 'Layout type',
                'icon' => '/icon.svg',
                'zones' => [
                    [
                        'identifier' => 'zone1',
                        'name' => 'Zone 1',
                        'allowed_block_definitions' => ['title'],
                    ],
                    [
                        'identifier' => 'zone2',
                        'name' => 'Zone 2',
                        'allowed_block_definitions' => true,
                    ],
                ],
            ],
            $this->normalizer->normalize(new VersionedValue($layoutType, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\LayoutTypeNormalizer::supportsNormalization
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
            [new LayoutType(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new LayoutType(), 2), false],
            [new VersionedValue(new LayoutType(), 1), true],
        ];
    }
}
