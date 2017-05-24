<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value as APIValue;
use PHPUnit\Framework\TestCase;

class CollectionItemNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemLoaderMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);

        $this->normalizer = new CollectionItemNormalizer(
            $this->itemLoaderMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::normalize
     */
    public function testNormalize()
    {
        $item = new CollectionItem(
            array(
                'id' => 42,
                'collectionId' => 24,
                'position' => 3,
                'type' => CollectionItem::TYPE_OVERRIDE,
                'valueId' => 12,
                'valueType' => 'ezcontent',
            )
        );

        $value = new Item(
            array(
                'name' => 'Value name',
                'isVisible' => true,
            )
        );

        $this->itemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo(12), $this->equalTo('ezcontent'))
            ->will($this->returnValue($value));

        $this->assertEquals(
            array(
                'id' => $item->getId(),
                'collection_id' => $item->getCollectionId(),
                'position' => $item->getPosition(),
                'type' => $item->getType(),
                'value_id' => $item->getValueId(),
                'value_type' => $item->getValueType(),
                'name' => 'Value name',
                'visible' => true,
            ),
            $this->normalizer->normalize(new VersionedValue($item, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::normalize
     */
    public function testNormalizeWithNoValue()
    {
        $item = new CollectionItem(
            array(
                'id' => 42,
                'collectionId' => 24,
                'position' => 3,
                'type' => CollectionItem::TYPE_OVERRIDE,
                'valueId' => 12,
                'valueType' => 'ezcontent',
            )
        );

        $this->itemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo(12), $this->equalTo('ezcontent'))
            ->will($this->throwException(new ItemException()));

        $this->assertEquals(
            array(
                'id' => $item->getId(),
                'collection_id' => $item->getCollectionId(),
                'position' => $item->getPosition(),
                'type' => $item->getType(),
                'value_id' => $item->getValueId(),
                'value_type' => $item->getValueType(),
                'name' => null,
                'visible' => null,
            ),
            $this->normalizer->normalize(new VersionedValue($item, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::supportsNormalization
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
            array(new CollectionItem(), false),
            array(new VersionedValue(new APIValue(), 1), false),
            array(new VersionedValue(new CollectionItem(), 2), false),
            array(new VersionedValue(new CollectionItem(), 1), true),
        );
    }
}
