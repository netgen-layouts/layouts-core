<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Collection\ResultItem;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultItemNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value as APIValue;

class CollectionResultItemNormalizerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultItemNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new CollectionResultItemNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultItemNormalizer::normalize
     */
    public function testNormalize()
    {
        $collectionItem = new CollectionItem(
            array(
                'id' => 42,
                'collectionId' => 24,
            )
        );

        $item = new Item(
            array(
                'name' => 'Value name',
                'isVisible' => true,
            )
        );

        $resultItem = new ResultItem(
            array(
                'item' => $item,
                'collectionItem' => $collectionItem,
                'type' => ResultItem::TYPE_MANUAL,
                'position' => 3,
            )
        );

        self::assertEquals(
            array(
                'id' => $collectionItem->getId(),
                'collection_id' => $collectionItem->getCollectionId(),
                'position' => $resultItem->getPosition(),
                'type' => $resultItem->getType(),
                'value_id' => $item->getValueId(),
                'value_type' => $item->getValueType(),
                'name' => $item->getName(),
                'visible' => $item->isVisible(),
            ),
            $this->normalizer->normalize(new VersionedValue($resultItem, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultItemNormalizer::normalize
     */
    public function testNormalizeWithoutCollectionItem()
    {
        $item = new Item(
            array(
                'name' => 'Value name',
                'isVisible' => true,
            )
        );

        $resultItem = new ResultItem(
            array(
                'item' => $item,
                'collectionItem' => null,
                'type' => ResultItem::TYPE_DYNAMIC,
                'position' => 3,
            )
        );

        self::assertEquals(
            array(
                'id' => null,
                'collection_id' => null,
                'position' => $resultItem->getPosition(),
                'type' => $resultItem->getType(),
                'value_id' => $item->getValueId(),
                'value_type' => $item->getValueType(),
                'name' => $item->getName(),
                'visible' => $item->isVisible(),
            ),
            $this->normalizer->normalize(new VersionedValue($resultItem, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultItemNormalizer::supportsNormalization
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
            array(new APIValue(), false),
            array(new ResultItem(), false),
            array(new VersionedValue(new APIValue(), 1), false),
            array(new VersionedValue(new ResultItem(), 2), false),
            array(new VersionedValue(new ResultItem(), 1), true),
        );
    }
}
