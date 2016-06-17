<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Collection\Result;
use Netgen\BlockManager\Collection\ResultItem;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultNormalizer;
use Netgen\BlockManager\Serializer\Values\ValueList;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Symfony\Component\Serializer\Serializer;
use PHPUnit\Framework\TestCase;

class CollectionResultNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new CollectionResultNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultNormalizer::normalize
     */
    public function testNormalize()
    {
        $result = new Result(
            array(
                'results' => array(
                    new ResultItem(),
                    new ResultItem(),
                ),
                'totalCount' => 5,
            )
        );

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with(
                $this->equalTo(
                    new ValueList(
                        array(
                            new VersionedValue(new ResultItem(), 1),
                            new VersionedValue(new ResultItem(), 1),
                        )
                    )
                )
            )
            ->will($this->returnValue(array('items')));

        self::assertEquals(
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
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionResultNormalizer::supportsNormalization
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
            array(new Result(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Result(), 2), false),
            array(new VersionedValue(new Result(), 1), true),
        );
    }
}
