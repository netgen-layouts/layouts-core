<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class CollectionReferenceNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionServiceMock;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->normalizer = new CollectionReferenceNormalizer(
            $this->collectionServiceMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer::normalize
     */
    public function testNormalize()
    {
        $collectionReference = new CollectionReference(
            array(
                'blockId' => 42,
                'collectionId' => 24,
                'collectionStatus' => Collection::STATUS_PUBLISHED,
                'identifier' => 'default',
                'offset' => 10,
                'limit' => 5,
            )
        );

        $collection = new Collection(
            array(
                'id' => 24,
                'type' => Collection::TYPE_MANUAL,
                'name' => null,
            )
        );

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadCollection')
            ->with($this->equalTo(24))
            ->will($this->returnValue($collection));

        self::assertEquals(
            array(
                'id' => $collection->getId(),
                'type' => $collection->getType(),
                'name' => $collection->getName(),
                'block_id' => $collectionReference->getBlockId(),
                'identifier' => $collectionReference->getIdentifier(),
                'offset' => $collectionReference->getOffset(),
                'limit' => $collectionReference->getLimit(),
            ),
            $this->normalizer->normalize(new VersionedValue($collectionReference, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer::normalize
     */
    public function testNormalizeDraft()
    {
        $collectionReference = new CollectionReference(
            array(
                'blockId' => 42,
                'collectionId' => 24,
                'collectionStatus' => Collection::STATUS_DRAFT,
                'identifier' => 'default',
                'offset' => 10,
                'limit' => 5,
            )
        );

        $collection = new Collection(
            array(
                'id' => 24,
                'type' => Collection::TYPE_MANUAL,
                'name' => null,
            )
        );

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadCollectionDraft')
            ->with($this->equalTo(24))
            ->will($this->returnValue($collection));

        self::assertEquals(
            array(
                'id' => $collection->getId(),
                'type' => $collection->getType(),
                'name' => $collection->getName(),
                'block_id' => $collectionReference->getBlockId(),
                'identifier' => $collectionReference->getIdentifier(),
                'offset' => $collectionReference->getOffset(),
                'limit' => $collectionReference->getLimit(),
            ),
            $this->normalizer->normalize(new VersionedValue($collectionReference, 1))
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
