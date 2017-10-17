<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionReferenceNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class CollectionReferenceNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionReferenceNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->normalizer = new CollectionReferenceNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionReferenceNormalizer::normalize
     */
    public function testNormalize()
    {
        $collection = new Collection(
            array(
                'id' => 24,
                'type' => Collection::TYPE_MANUAL,
                'status' => Value::STATUS_PUBLISHED,
                'offset' => 10,
                'limit' => 5,
            )
        );

        $collectionReference = new CollectionReference(
            array(
                'collection' => $collection,
                'identifier' => 'default',
            )
        );

        $this->assertEquals(
            array(
                'identifier' => $collectionReference->getIdentifier(),
                'collection_id' => $collection->getId(),
                'collection_type' => $collection->getType(),
                'offset' => $collection->getOffset(),
                'limit' => $collection->getLimit(),
            ),
            $this->normalizer->normalize(new VersionedValue($collectionReference, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionReferenceNormalizer::normalize
     */
    public function testNormalizeDraft()
    {
        $collection = new Collection(
            array(
                'id' => 24,
                'type' => Collection::TYPE_MANUAL,
                'status' => Value::STATUS_DRAFT,
                'offset' => 10,
                'limit' => 5,
            )
        );

        $collectionReference = new CollectionReference(
            array(
                'collection' => $collection,
                'identifier' => 'default',
            )
        );

        $this->assertEquals(
            array(
                'identifier' => $collectionReference->getIdentifier(),
                'collection_id' => $collection->getId(),
                'collection_type' => $collection->getType(),
                'offset' => $collection->getOffset(),
                'limit' => $collection->getLimit(),
            ),
            $this->normalizer->normalize(new VersionedValue($collectionReference, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionReferenceNormalizer::supportsNormalization
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
            array(new Value(), false),
            array(new CollectionReference(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new CollectionReference(), 2), false),
            array(new VersionedValue(new CollectionReference(), 1), true),
        );
    }
}
