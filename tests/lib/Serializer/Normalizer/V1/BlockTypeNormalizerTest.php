<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class BlockTypeNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeNormalizer
     */
    protected $normalizer;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new BlockDefinition('title');

        $this->normalizer = new BlockTypeNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeNormalizer::normalize
     */
    public function testNormalize()
    {
        $blockType = new BlockType(
            array(
                'identifier' => 'identifier',
                'name' => 'Block type',
                'definition' => $this->blockDefinition,
                'defaults' => array(
                    'name' => 'Default name',
                    'view_type' => 'Default view type',
                    'parameters' => array('param' => 'value'),
                ),
            )
        );

        $this->assertEquals(
            array(
                'identifier' => $blockType->getIdentifier(),
                'name' => $blockType->getName(),
                'definition_identifier' => $this->blockDefinition->getIdentifier(),
                'is_container' => false,
                'is_dynamic_container' => false,
                'defaults' => $blockType->getDefaults(),
            ),
            $this->normalizer->normalize(new VersionedValue($blockType, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockTypeNormalizer::supportsNormalization
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
            array(new BlockType(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new BlockType(), 2), false),
            array(new VersionedValue(new BlockType(), 1), true),
        );
    }
}
