<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\BlockTypeNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class BlockTypeNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\BlockTypeNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new BlockTypeNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\BlockTypeNormalizer::normalize
     */
    public function testNormalize()
    {
        $blockType = new BlockType(
            'identifier',
            true,
            'Block type',
            'title',
            array(
                'name' => 'Default name',
                'view_type' => 'Default view type',
                'parameters' => array('param' => 'value'),
            )
        );

        self::assertEquals(
            array(
                'identifier' => $blockType->getIdentifier(),
                'name' => $blockType->getName(),
                'definition_identifier' => $blockType->getDefinitionIdentifier(),
                'defaults' => $blockType->getDefaults(),
            ),
            $this->normalizer->normalize(new VersionedValue($blockType, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\BlockTypeNormalizer::supportsNormalization
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
            array(new BlockType('identifier', true, 'name', 'definition'), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new BlockType('identifier', true, 'name', 'definition'), 2), false),
            array(new VersionedValue(new BlockType('identifier', true, 'name', 'definition'), 1), true),
        );
    }
}
