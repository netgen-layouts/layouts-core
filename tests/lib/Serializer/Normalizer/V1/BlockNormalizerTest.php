<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

final class BlockNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $serializerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $blockServiceMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer
     */
    private $normalizer;

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
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::normalizeBlockCollections
     */
    public function testNormalize()
    {
        $collection = new Collection(
            [
                'id' => 24,
                'status' => Value::STATUS_PUBLISHED,
                'offset' => 10,
                'limit' => 5,
            ]
        );

        $collectionReference = new CollectionReference(
            [
                'collection' => $collection,
                'identifier' => 'default',
            ]
        );

        $block = new Block(
            [
                'id' => 42,
                'layoutId' => 24,
                'definition' => new BlockDefinition(),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'parentPosition' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'placeholders' => [
                    'main' => new Placeholder(['identifier' => 'main']),
                ],
                'collectionReferences' => [
                    'default' => $collectionReference,
                ],
                'isTranslatable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
                'parameters' => [
                    'some_param' => new Parameter(
                        [
                            'name' => 'some_param',
                            'value' => 'some_value',
                        ]
                    ),
                    'some_other_param' => new Parameter(
                        [
                            'name' => 'some_other_param',
                            'value' => 'some_other_value',
                        ]
                    ),
                ],
            ]
        );

        $serializedParams = [
            'some_param' => 'some_value',
            'some_other_param' => 'some_other_value',
        ];

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->will($this->returnValue($serializedParams));

        $this->serializerMock
            ->expects($this->at(1))
            ->method('normalize')
            ->with($this->equalTo([new VersionedValue(new Placeholder(['identifier' => 'main']), 1)]))
            ->will($this->returnValue(['normalized placeholders']));

        $this->blockServiceMock
            ->expects($this->once())
            ->method('hasPublishedState')
            ->with($this->equalTo($block))
            ->will($this->returnValue(true));

        $this->assertEquals(
            [
                'id' => $block->getId(),
                'layout_id' => $block->getLayoutId(),
                'definition_identifier' => $block->getDefinition()->getIdentifier(),
                'name' => $block->getName(),
                'parent_position' => $block->getParentPosition(),
                'parameters' => $serializedParams,
                'view_type' => $block->getViewType(),
                'item_view_type' => $block->getItemViewType(),
                'published' => true,
                'has_published_state' => true,
                'locale' => $block->getLocale(),
                'is_translatable' => $block->isTranslatable(),
                'always_available' => $block->isAlwaysAvailable(),
                'is_container' => false,
                'is_dynamic_container' => false,
                'placeholders' => ['normalized placeholders'],
                'collections' => [
                    [
                        'identifier' => 'default',
                        'collection_id' => $collection->getId(),
                        'collection_type' => $collection->getType(),
                        'offset' => $collection->getOffset(),
                        'limit' => $collection->getLimit(),
                    ],
                ],
            ],
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
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new Block(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Block(), 2), false],
            [new VersionedValue(new Block(), 1), true],
        ];
    }
}
