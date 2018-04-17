<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\Slot;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value as APIValue;
use PHPUnit\Framework\TestCase;

final class CollectionResultNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGeneratorMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->normalizer = new CollectionResultNormalizer($this->urlGeneratorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalize()
    {
        $collectionItem = new CollectionItem(
            [
                'id' => 42,
                'collectionId' => 24,
                'cmsItem' => new Item(
                    [
                        'name' => 'Value name',
                        'isVisible' => true,
                    ]
                ),
                'configs' => [
                    'visibility' => new Config(
                        [
                            'parameters' => [
                                'visibility_status' => new Parameter(
                                    [
                                        'value' => CollectionItem::VISIBILITY_VISIBLE,
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $result = new Result(3, new ManualItem($collectionItem));
        $this->urlGeneratorMock
            ->expects($this->any())
            ->method('generate')
            ->with($this->equalTo($collectionItem->getCmsItem()))
            ->will($this->returnValue('/some/url'));

        $this->assertEquals(
            [
                'id' => $collectionItem->getId(),
                'collection_id' => $collectionItem->getCollectionId(),
                'visible' => $collectionItem->isVisible(),
                'scheduled' => $collectionItem->isScheduled(),
                'is_dynamic' => false,
                'value' => $collectionItem->getCmsItem()->getValue(),
                'value_type' => $collectionItem->getCmsItem()->getValueType(),
                'name' => $collectionItem->getCmsItem()->getName(),
                'cms_visible' => $collectionItem->getCmsItem()->isVisible(),
                'cms_url' => '/some/url',
                'position' => $result->getPosition(),
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalizeWithNullItemInCollection()
    {
        $collectionItem = new CollectionItem(
            [
                'id' => 42,
                'collectionId' => 24,
                'cmsItem' => new NullItem(42),
                'configs' => [
                    'visibility' => new Config(
                        [
                            'parameters' => [
                                'visibility_status' => new Parameter(
                                    [
                                        'value' => CollectionItem::VISIBILITY_VISIBLE,
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $result = new Result(3, new ManualItem($collectionItem));
        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->assertEquals(
            [
                'id' => $collectionItem->getId(),
                'collection_id' => $collectionItem->getCollectionId(),
                'visible' => $collectionItem->isVisible(),
                'scheduled' => $collectionItem->isScheduled(),
                'is_dynamic' => false,
                'value' => $collectionItem->getCmsItem()->getValue(),
                'value_type' => $collectionItem->getCmsItem()->getValueType(),
                'name' => $collectionItem->getCmsItem()->getName(),
                'cms_visible' => $collectionItem->getCmsItem()->isVisible(),
                'cms_url' => null,
                'position' => $result->getPosition(),
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalizeWithoutCollectionItem()
    {
        $item = new Item(
            [
                'name' => 'Value name',
                'isVisible' => true,
            ]
        );

        $result = new Result(3, $item);

        $this->urlGeneratorMock
            ->expects($this->any())
            ->method('generate')
            ->with($this->equalTo($item))
            ->will($this->returnValue('/some/url'));

        $this->assertEquals(
            [
                'id' => null,
                'collection_id' => null,
                'visible' => true,
                'scheduled' => false,
                'is_dynamic' => true,
                'value' => $item->getValue(),
                'value_type' => $item->getValueType(),
                'name' => $item->getName(),
                'cms_visible' => $item->isVisible(),
                'cms_url' => '/some/url',
                'position' => $result->getPosition(),
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalizeWithSlot()
    {
        $item = new Slot();

        $result = new Result(3, $item);

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->assertEquals(
            [
                'id' => null,
                'collection_id' => null,
                'visible' => true,
                'scheduled' => false,
                'is_dynamic' => true,
                'value' => $item->getValue(),
                'value_type' => $item->getValueType(),
                'name' => $item->getName(),
                'cms_visible' => $item->isVisible(),
                'cms_url' => null,
                'position' => $result->getPosition(),
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalizeWithSubItem()
    {
        $collectionItem = new CollectionItem(
            [
                'id' => 42,
                'collectionId' => 24,
                'cmsItem' => new Item(
                    [
                        'name' => 'Value name',
                        'isVisible' => true,
                    ]
                ),
                'configs' => [
                    'visibility' => new Config(
                        [
                            'parameters' => [
                                'visibility_status' => new Parameter(
                                    [
                                        'value' => CollectionItem::VISIBILITY_VISIBLE,
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $item = new Item(
            [
                'name' => 'Value name',
                'isVisible' => true,
            ]
        );

        $result = new Result(3, new ManualItem($collectionItem), $item);
        $this->urlGeneratorMock
            ->expects($this->any())
            ->method('generate')
            ->with($this->equalTo($collectionItem->getCmsItem()))
            ->will($this->returnValue('/some/url'));

        $this->assertEquals(
            [
                'id' => null,
                'collection_id' => null,
                'visible' => true,
                'scheduled' => false,
                'is_dynamic' => true,
                'value' => $item->getValue(),
                'value_type' => $item->getValueType(),
                'name' => $item->getName(),
                'cms_visible' => $item->isVisible(),
                'cms_url' => '/some/url',
                'position' => $result->getPosition(),
                'override_item' => [
                    'id' => $collectionItem->getId(),
                    'collection_id' => $collectionItem->getCollectionId(),
                    'visible' => $collectionItem->isVisible(),
                    'scheduled' => $collectionItem->isScheduled(),
                    'is_dynamic' => false,
                    'value' => $collectionItem->getCmsItem()->getValue(),
                    'value_type' => $collectionItem->getCmsItem()->getValueType(),
                    'name' => $collectionItem->getCmsItem()->getName(),
                    'cms_visible' => $collectionItem->getCmsItem()->isVisible(),
                    'cms_url' => '/some/url',
                ],
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::supportsNormalization
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
            [new APIValue(), false],
            [new Result(0, new Item()), false],
            [new VersionedValue(new APIValue(), 1), false],
            [new VersionedValue(new Result(0, new Item()), 2), false],
            [new VersionedValue(new Result(0, new Item()), 1), true],
        ];
    }
}
