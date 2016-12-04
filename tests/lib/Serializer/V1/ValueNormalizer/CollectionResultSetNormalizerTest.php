<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultSetNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class CollectionResultSetNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultSetNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new CollectionResultSetNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultSetNormalizer::normalize
     */
    public function testNormalize()
    {
        $result = new ResultSet(
            array(
                'results' => array(
                    new Result(),
                    new Result(),
                ),
                'totalCount' => 5,
            )
        );

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with(
                $this->equalTo(
                    array(
                        new VersionedValue(new Result(), 1),
                        new VersionedValue(new Result(), 1),
                    )
                )
            )
            ->will($this->returnValue(array('items')));

        $this->assertEquals(
            array(
                'items' => array('items'),
                'item_count' => 5,
            ),
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultSetNormalizer::supportsNormalization
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
            array(new ResultSet(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new ResultSet(), 2), false),
            array(new VersionedValue(new ResultSet(), 1), true),
        );
    }
}
