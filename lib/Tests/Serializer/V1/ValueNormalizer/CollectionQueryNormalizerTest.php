<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionQueryNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class CollectionQueryNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionQueryNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new CollectionQueryNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionQueryNormalizer::normalize
     */
    public function testNormalize()
    {
        $query = new Query(
            array(
                'id' => 42,
                'collectionId' => 24,
                'position' => 3,
                'identifier' => 'default',
                'queryType' => new QueryType('ezcontent_search'),
                'parameters' => array(
                    'param' => 'value',
                    'param2' => array(
                        'param3' => 'value3',
                    ),
                ),
            )
        );

        $this->assertEquals(
            array(
                'id' => $query->getId(),
                'collection_id' => $query->getCollectionId(),
                'position' => $query->getPosition(),
                'identifier' => $query->getIdentifier(),
                'type' => $query->getQueryType()->getType(),
                'parameters' => $query->getParameters(),
            ),
            $this->normalizer->normalize(new VersionedValue($query, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionQueryNormalizer::supportsNormalization
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
            array(new Query(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Query(), 2), false),
            array(new VersionedValue(new Query(), 1), true),
        );
    }
}
