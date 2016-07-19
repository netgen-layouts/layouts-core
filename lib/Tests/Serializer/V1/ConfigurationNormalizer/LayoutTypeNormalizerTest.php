<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone;
use Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\LayoutTypeNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class LayoutTypeNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\LayoutTypeNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new LayoutTypeNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\LayoutTypeNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\LayoutTypeNormalizer::getZones
     */
    public function testNormalize()
    {
        $layoutType = new LayoutType(
            '4_zones_a',
            true,
            'Layout type',
            array(
                'zone1' => new Zone('zone1', 'Zone 1', array('title')),
                'zone2' => new Zone('zone2', 'Zone 2', array()),
            )
        );

        self::assertEquals(
            array(
                'identifier' => $layoutType->getIdentifier(),
                'name' => $layoutType->getName(),
                'zones' => array_map(
                    function (Zone $zone) {
                        return array(
                            'identifier' => $zone->getIdentifier(),
                            'name' => $zone->getName(),
                            'allowed_block_definitions' => !empty($zone->getAllowedBlockDefinitions()) ?
                                $zone->getAllowedBlockDefinitions() :
                                true,
                        );
                    },
                    array_values($layoutType->getZones())
                ),
            ),
            $this->normalizer->normalize(new VersionedValue($layoutType, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\LayoutTypeNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        self::assertEquals($expected, $this->normalizer->supportsNormalization($data));
    }

    /**
     * Provider for {@link self::testSupportsNormalization}.
     *
     * @return array
     */
    public function supportsNormalizationProvider()
    {
        return array(
            array(null, false),
            array(true, false),
            array(false, false),
            array('block', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new LayoutType('4_zones_a', true, '4 zones A', array()), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new LayoutType('4_zones_a', true, '4 zones A', array()), 2), false),
            array(new VersionedValue(new LayoutType('4_zones_a', true, '4 zones A', array()), 1), true),
        );
    }
}
