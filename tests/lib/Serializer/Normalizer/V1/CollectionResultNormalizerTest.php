<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\Slot;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Item\UrlBuilderInterface;
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
    private $urlBuilderMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->urlBuilderMock = $this->createMock(UrlBuilderInterface::class);

        $this->normalizer = new CollectionResultNormalizer($this->urlBuilderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalize()
    {
        $collectionItem = new CollectionItem(
            array(
                'id' => 42,
                'collectionId' => 24,
                'cmsItem' => new Item(
                    array(
                        'name' => 'Value name',
                        'isVisible' => true,
                    )
                ),
                'configs' => array(
                    'visibility' => new Config(
                        array(
                            'parameters' => array(
                                'visibility_status' => new Parameter(
                                    array(
                                        'value' => CollectionItem::VISIBILITY_VISIBLE,
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $result = new Result(3, new ManualItem($collectionItem));
        $this->urlBuilderMock
            ->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo($collectionItem->getCmsItem()))
            ->will($this->returnValue('/some/url'));

        $this->assertEquals(
            array(
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
            ),
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
            array(
                'id' => 42,
                'collectionId' => 24,
                'cmsItem' => new NullItem(42),
                'configs' => array(
                    'visibility' => new Config(
                        array(
                            'parameters' => array(
                                'visibility_status' => new Parameter(
                                    array(
                                        'value' => CollectionItem::VISIBILITY_VISIBLE,
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $result = new Result(3, new ManualItem($collectionItem));
        $this->urlBuilderMock
            ->expects($this->never())
            ->method('getUrl');

        $this->assertEquals(
            array(
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
            ),
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
            array(
                'id' => 42,
                'collectionId' => 24,
                'cmsItem' => new Item(
                    array(
                        'name' => 'Value name',
                        'isVisible' => true,
                    )
                ),
                'configs' => array(
                    'visibility' => new Config(
                        array(
                            'parameters' => array(
                                'visibility_status' => new Parameter(
                                    array(
                                        'value' => CollectionItem::VISIBILITY_VISIBLE,
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $item = new Item(
            array(
                'name' => 'Value name',
                'isVisible' => true,
            )
        );

        $result = new Result(3, new ManualItem($collectionItem), $item);
        $this->urlBuilderMock
            ->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo($collectionItem->getCmsItem()))
            ->will($this->returnValue('/some/url'));

        $this->assertEquals(
            array(
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
                'sub_item' => array(
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
                ),
            ),
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
            array(
                'name' => 'Value name',
                'isVisible' => true,
            )
        );

        $result = new Result(3, $item);

        $this->urlBuilderMock
            ->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo($item))
            ->will($this->returnValue('/some/url'));

        $this->assertEquals(
            array(
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
            ),
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

        $this->urlBuilderMock
            ->expects($this->never())
            ->method('getUrl');

        $this->assertEquals(
            array(
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
            ),
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
        return array(
            array(null, false),
            array(true, false),
            array(false, false),
            array('block', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new APIValue(), false),
            array(new Result(0, new Item()), false),
            array(new VersionedValue(new APIValue(), 1), false),
            array(new VersionedValue(new Result(0, new Item()), 2), false),
            array(new VersionedValue(new Result(0, new Item()), 1), true),
        );
    }
}
