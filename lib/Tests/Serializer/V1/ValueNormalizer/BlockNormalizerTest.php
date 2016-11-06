<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\BlockNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class BlockNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

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
        $this->serializerMock = $this->createMock(Serializer::class);
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->normalizer = new BlockNormalizer($this->blockServiceMock);
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\BlockNormalizer::__construct
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
                'blockDefinition' => new BlockDefinition('text'),
                'parameters' => array(
                    'some_param' => new ParameterValue(
                        array(
                            'name' => 'some_param',
                            'value' => 'some_value',
                        )
                    ),
                    'some_other_param' => new ParameterValue(
                        array(
                            'name' => 'some_other_param',
                            'value' => 'some_other_value',
                        )
                    ),
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'status' => Value::STATUS_PUBLISHED,
                'published' => true,
                'name' => 'My block',
            )
        );

        $serializedParams = array(
            'some_param' => 'some_value',
            'some_other_param' => 'some_other_value',
        );

        $this->serializerMock
            ->expects($this->once())
            ->method('normalize')
            ->will($this->returnValue($serializedParams));

        $this->blockServiceMock
            ->expects($this->once())
            ->method('hasPublishedState')
            ->with($this->equalTo($block))
            ->will($this->returnValue(true));

        $this->assertEquals(
            array(
                'id' => $block->getId(),
                'definition_identifier' => $block->getBlockDefinition()->getIdentifier(),
                'name' => $block->getName(),
                'zone_identifier' => $block->getZoneIdentifier(),
                'position' => 2,
                'layout_id' => $block->getLayoutId(),
                'parameters' => $serializedParams,
                'view_type' => $block->getViewType(),
                'item_view_type' => $block->getItemViewType(),
                'published' => true,
                'has_published_state' => true,
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
            array(new Block(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Block(), 2), false),
            array(new VersionedValue(new Block(), 1), true),
        );
    }
}
