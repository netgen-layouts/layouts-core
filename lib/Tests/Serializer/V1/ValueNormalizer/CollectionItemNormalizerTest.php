<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value as APIValue;
use Netgen\BlockManager\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class CollectionItemNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemBuilderMock;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);

        $this->normalizer = new CollectionItemNormalizer(
            $this->itemBuilderMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer::normalize
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

        $this->itemBuilderMock
            ->expects($this->any())
            ->method('build')
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
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer::normalize
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

        $this->itemBuilderMock
            ->expects($this->any())
            ->method('build')
            ->with($this->equalTo(12), $this->equalTo('ezcontent'))
            ->will($this->throwException(new RuntimeException()));

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
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer::supportsNormalization
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
