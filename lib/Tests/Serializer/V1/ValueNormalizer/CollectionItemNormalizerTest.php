<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface;
use Netgen\BlockManager\Collection\ResultValue;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;

class CollectionItemNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultValueBuilderMock;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->resultValueBuilderMock = $this->getMock(ResultValueBuilderInterface::class);

        $this->normalizer = new CollectionItemNormalizer(
            $this->resultValueBuilderMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer::normalize
     */
    public function testNormalize()
    {
        $item = new Item(
            array(
                'id' => 42,
                'collectionId' => 24,
                'position' => 3,
                'type' => Item::TYPE_OVERRIDE,
                'valueId' => 12,
                'valueType' => 'ezcontent',
            )
        );

        $resultValue = new ResultValue();
        $resultValue->name = 'Value name';
        $resultValue->isVisible = true;

        $this->resultValueBuilderMock
            ->expects($this->any())
            ->method('buildFromItem')
            ->with($item)
            ->will($this->returnValue($resultValue));

        self::assertEquals(
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
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionItemNormalizer::supportsNormalization
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
            array(new Value(), false),
            array(new Item(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Item(), 2), false),
            array(new VersionedValue(new Item(), 1), true),
        );
    }
}
