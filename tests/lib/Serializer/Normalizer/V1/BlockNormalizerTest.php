<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BlockNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $normalizerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $blockServiceMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->normalizer = new BlockNormalizer($this->blockServiceMock);
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer::setNormalizer
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::normalizeBlockCollections
     */
    public function testNormalize(): void
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
                'definition' => new BlockDefinition(['identifier' => 'definition']),
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
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
            ]
        );

        $serializedParams = [
            'some_param' => 'some_value',
            'some_other_param' => 'some_other_value',
        ];

        $this->normalizerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->will($this->returnValue($serializedParams));

        $this->normalizerMock
            ->expects($this->at(1))
            ->method('normalize')
            ->with($this->equalTo([new VersionedValue(new Placeholder(['identifier' => 'main']), 1)]))
            ->will($this->returnValue(['normalized placeholders']));

        $serializedConfig = [
            'key' => [
                'param1' => 'value1',
                'param2' => 'value2',
            ],
        ];

        $this->normalizerMock
            ->expects($this->at(2))
            ->method('normalize')
            ->will($this->returnValue($serializedConfig));

        $this->blockServiceMock
            ->expects($this->once())
            ->method('hasPublishedState')
            ->with($this->equalTo($block))
            ->will($this->returnValue(true));

        $this->assertSame(
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
                'config' => $serializedConfig,
            ],
            $this->normalizer->normalize(new VersionedValue($block, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::normalize
     */
    public function testNormalizeWithContainerBlock(): void
    {
        $block = new Block(
            [
                'id' => 42,
                'layoutId' => 24,
                'definition' => new ContainerDefinition(
                    [
                        'identifier' => 'definition',
                        'handler' => new ContainerDefinitionHandler(),
                    ]
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'parentPosition' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'placeholders' => [],
                'collectionReferences' => [],
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
                'parameters' => [],
            ]
        );

        $data = $this->normalizer->normalize(new VersionedValue($block, 1));

        $this->assertTrue($data['is_container']);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        $this->assertSame($expected, $this->normalizer->supportsNormalization($data));
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
            [new Block(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Block(), 2), false],
            [new VersionedValue(new Block(), 1), true],
        ];
    }
}
