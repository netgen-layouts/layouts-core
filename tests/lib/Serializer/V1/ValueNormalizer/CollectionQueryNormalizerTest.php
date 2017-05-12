<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionQueryNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class CollectionQueryNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionQueryNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new CollectionQueryNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
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
                'queryType' => new QueryType('ezcontent_search'),
                'parameters' => array(
                    'param' => new ParameterValue(
                        array(
                            'name' => 'param',
                            'value' => 'value',
                        )
                    ),
                    'param2' => new ParameterValue(
                        array(
                            'name' => 'param2',
                            'value' => array(
                                'param3' => 'value3',
                            ),
                        )
                    ),
                ),
            )
        );

        $serializedParams = array(
            'param' => 'value',
            'param2' => array(
                'param3' => 'value3',
            ),
        );

        $this->serializerMock
            ->expects($this->once())
            ->method('normalize')
            ->will($this->returnValue($serializedParams));

        $this->assertEquals(
            array(
                'id' => $query->getId(),
                'collection_id' => $query->getCollectionId(),
                'type' => $query->getQueryType()->getType(),
                'parameters' => $serializedParams,
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
