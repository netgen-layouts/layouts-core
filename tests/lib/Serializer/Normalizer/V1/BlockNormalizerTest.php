<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\BlockTranslation;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer;
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
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer
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
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::normalize
     */
    public function testNormalize()
    {
        $block = new Block(
            array(
                'id' => 42,
                'layoutId' => 24,
                'definition' => new BlockDefinition('text'),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'status' => Value::STATUS_PUBLISHED,
                'published' => true,
                'placeholders' => array(
                    'main' => new Placeholder(array('identifier' => 'main')),
                ),
                'isTranslatable' => true,
                'availableLocales' => array('en'),
                'mainLocale' => 'en',
                'translations' => array(
                    'en' => new BlockTranslation(
                        array(
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
                        )
                    ),
                ),
            )
        );

        $serializedParams = array(
            'some_param' => 'some_value',
            'some_other_param' => 'some_other_value',
        );

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->will($this->returnValue($serializedParams));

        $this->serializerMock
            ->expects($this->at(1))
            ->method('normalize')
            ->with($this->equalTo(array(new VersionedValue(new Placeholder(array('identifier' => 'main')), 1))))
            ->will($this->returnValue(array('normalized placeholders')));

        $this->blockServiceMock
            ->expects($this->once())
            ->method('hasPublishedState')
            ->with($this->equalTo($block))
            ->will($this->returnValue(true));

        $this->assertEquals(
            array(
                'id' => $block->getId(),
                'layout_id' => $block->getLayoutId(),
                'definition_identifier' => $block->getDefinition()->getIdentifier(),
                'name' => $block->getName(),
                'parameters' => $serializedParams,
                'view_type' => $block->getViewType(),
                'item_view_type' => $block->getItemViewType(),
                'published' => true,
                'has_published_state' => true,
                'locale' => $block->getTranslation()->getLocale(),
                'is_translatable' => $block->isTranslatable(),
                'always_available' => $block->isAlwaysAvailable(),
                'is_container' => false,
                'is_dynamic_container' => false,
                'placeholders' => array('normalized placeholders'),
            ),
            $this->normalizer->normalize(new VersionedValue($block, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::supportsNormalization
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
