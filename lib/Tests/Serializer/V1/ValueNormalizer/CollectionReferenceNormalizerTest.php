<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;

class CollectionReferenceNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new CollectionReferenceNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer::normalize
     */
    public function testNormalize()
    {
        $collection = new CollectionReference(
            array(
                'blockId' => 42,
                'collectionId' => 24,
                'identifier' => 'default',
                'offset' => 10,
                'limit' => 5,
            )
        );

        self::assertEquals(
            array(
                'block_id' => $collection->getBlockId(),
                'collection_id' => $collection->getCollectionId(),
                'identifier' => $collection->getIdentifier(),
                'offset' => $collection->getOffset(),
                'limit' => $collection->getLimit(),
            ),
            $this->normalizer->normalize(new VersionedValue($collection, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer::supportsNormalization
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
            array(new CollectionReference(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new CollectionReference(), 2), false),
            array(new VersionedValue(new CollectionReference(), 1), true),
        );
    }
}
