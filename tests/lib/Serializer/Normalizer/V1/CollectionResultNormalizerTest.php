<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value as APIValue;
use PHPUnit\Framework\TestCase;

class CollectionResultNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->normalizer = new CollectionResultNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
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

        $result = new Result(
            array(
                'item' => $item,
                'collectionItem' => $collectionItem,
                'type' => Result::TYPE_MANUAL,
                'position' => 3,
            )
        );

        $this->assertEquals(
            array(
                'id' => $collectionItem->getId(),
                'collection_id' => $collectionItem->getCollectionId(),
                'position' => $result->getPosition(),
                'type' => $result->getType(),
                'value_id' => $item->getValueId(),
                'value_type' => $item->getValueType(),
                'name' => $item->getName(),
                'visible' => $item->isVisible(),
            ),
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     */
    public function testNormalizeWithoutCollectionItem()
    {
        $item = new Item(
            array(
                'name' => 'Value name',
                'isVisible' => true,
            )
        );

        $result = new Result(
            array(
                'item' => $item,
                'collectionItem' => null,
                'type' => Result::TYPE_DYNAMIC,
                'position' => 3,
            )
        );

        $this->assertEquals(
            array(
                'id' => null,
                'collection_id' => null,
                'position' => $result->getPosition(),
                'type' => $result->getType(),
                'value_id' => $item->getValueId(),
                'value_type' => $item->getValueType(),
                'name' => $item->getName(),
                'visible' => $item->isVisible(),
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
            array(new Result(), false),
            array(new VersionedValue(new APIValue(), 1), false),
            array(new VersionedValue(new Result(), 2), false),
            array(new VersionedValue(new Result(), 1), true),
        );
    }
}
