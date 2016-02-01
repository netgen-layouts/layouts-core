<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer;
use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\Tests\API\Stubs\Value;

class BlockNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer::normalize
     */
    public function testNormalize()
    {
        $block = new Block(
            array(
                'id' => 42,
                'layoutId' => 24,
                'zoneIdentifier' => 'bottom',
                'definitionIdentifier' => 'paragraph',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
                'name' => 'My block',
            )
        );

        $blockNormalizer = new BlockNormalizer();

        self::assertEquals(
            array(
                'id' => $block->getId(),
                'definition_identifier' => $block->getDefinitionIdentifier(),
                'name' => $block->getName(),
                'zone_identifier' => $block->getZoneIdentifier(),
                'layout_id' => $block->getLayoutId(),
                'parameters' => $block->getParameters(),
                'view_type' => $block->getViewType(),
            ),
            $blockNormalizer->normalize(new SerializableValue($block, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $blockNormalizer = new BlockNormalizer();

        self::assertEquals($expected, $blockNormalizer->supportsNormalization($data));
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
            array(new Block(), false),
            array(new SerializableValue(new Value(), 1), false),
            array(new SerializableValue(new Block(), 2), false),
            array(new SerializableValue(new Block(), 1), true),
        );
    }
}
