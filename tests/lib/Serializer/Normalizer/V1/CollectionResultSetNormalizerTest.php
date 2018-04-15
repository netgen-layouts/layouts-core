<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultSetNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

final class CollectionResultSetNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultSetNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new CollectionResultSetNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultSetNormalizer::getOverflowItems
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultSetNormalizer::normalize
     */
    public function testNormalize()
    {
        $result = new ResultSet(
            array(
                'collection' => new Collection(
                    array(
                        'items' => new ArrayCollection(
                            array(
                                new Item(array('position' => 0)),
                                new Item(array('position' => 1)),
                                new Item(array('position' => 2)),
                                new Item(array('position' => 3)),
                            )
                        ),
                    )
                ),
                'results' => array(
                    new Result(1, new ManualItem(new Item(array('position' => 1)))),
                    new Result(2, new ManualItem(new Item(array('position' => 2)))),
                ),
            )
        );

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with(
                $this->equalTo(
                    array(
                        new VersionedValue(new Result(1, new ManualItem(new Item(array('position' => 1)))), 1),
                        new VersionedValue(new Result(2, new ManualItem(new Item(array('position' => 2)))), 1),
                    )
                )
            )
            ->will($this->returnValue(array('items')));

        $this->serializerMock
            ->expects($this->at(1))
            ->method('normalize')
            ->with(
                $this->equalTo(
                    array(
                        new VersionedValue(new Item(array('position' => 0)), 1),
                        new VersionedValue(new Item(array('position' => 3)), 1),
                    )
                )
            )
            ->will($this->returnValue(array('overflow_items')));

        $this->assertEquals(
            array(
                'items' => array('items'),
                'overflow_items' => array('overflow_items'),
            ),
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultSetNormalizer::supportsNormalization
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
