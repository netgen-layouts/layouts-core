<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\BlockNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;

class BlockNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\BlockNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->blockServiceMock = $this->getMock(BlockService::class);

        $this->normalizer = new BlockNormalizer($this->blockServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\BlockNormalizer::normalize
     */
    public function testNormalize()
    {
        $block = new Block(
            array(
                'id' => 42,
                'layoutId' => 24,
                'zoneIdentifier' => 'bottom',
                'position' => 2,
                'definitionIdentifier' => 'paragraph',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
            )
        );

        $this->blockServiceMock
            ->expects($this->once())
            ->method('isPublished')
            ->with($this->equalTo($block))
            ->will($this->returnValue(true));

        self::assertEquals(
            array(
                'id' => $block->getId(),
                'definition_identifier' => $block->getDefinitionIdentifier(),
                'name' => $block->getName(),
                'zone_identifier' => $block->getZoneIdentifier(),
                'position' => 2,
                'layout_id' => $block->getLayoutId(),
                'parameters' => $block->getParameters(),
                'view_type' => $block->getViewType(),
                'item_view_type' => $block->getItemViewType(),
                'is_published' => true,
            ),
            $this->normalizer->normalize(new VersionedValue($block, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\BlockNormalizer::supportsNormalization
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
            array(new Block(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Block(), 2), false),
            array(new VersionedValue(new Block(), 1), true),
        );
    }
}
