<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\BlockTypeGroupNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Configuration\Stubs\BlockType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class BlockTypeGroupNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\BlockTypeGroupNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new BlockTypeGroupNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\BlockTypeGroupNormalizer::normalize
     */
    public function testNormalize()
    {
        $blockTypeGroup = new BlockTypeGroup(
            array(
                'identifier' => 'identifier',
                'name' => 'Block group',
                'blockTypes' => array(new BlockType(array('identifier' => 'type'))),
            )
        );

        $this->assertEquals(
            array(
                'identifier' => $blockTypeGroup->getIdentifier(),
                'name' => $blockTypeGroup->getName(),
                'block_types' => array('type'),
            ),
            $this->normalizer->normalize(new VersionedValue($blockTypeGroup, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\BlockTypeGroupNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $this->assertEquals($expected, $this->normalizer->supportsNormalization($data));
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
            array(new BlockTypeGroup(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new BlockTypeGroup(), 2), false),
            array(new VersionedValue(new BlockTypeGroup(), 1), true),
        );
    }
}
