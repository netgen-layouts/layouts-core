<?php

namespace Netgen\BlockManager\Tests\Serializer\ValueNormalizer;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\ValueNormalizer\CollectionNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Symfony\Component\Serializer\Serializer;

class CollectionNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\ValueNormalizer\CollectionNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->getMock(Serializer::class);

        $this->normalizer = new CollectionNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\ValueNormalizer\CollectionNormalizer::normalize
     */
    public function testNormalize()
    {
        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->will($this->returnValue(array('manual_items')));

        $this->serializerMock
            ->expects($this->at(1))
            ->method('normalize')
            ->will($this->returnValue(array('override_items')));

        $this->serializerMock
            ->expects($this->at(2))
            ->method('normalize')
            ->will($this->returnValue(array('queries')));

        $collection = new Collection(
            array(
                'id' => 42,
                'type' => Collection::TYPE_NAMED,
                'name' => 'My collection',
            )
        );

        self::assertEquals(
            array(
                'id' => $collection->getId(),
                'type' => $collection->getType(),
                'name' => $collection->getName(),
                'manual_items' => array('manual_items'),
                'override_items' => array('override_items'),
                'queries' => array('queries'),
            ),
            $this->normalizer->normalize(new VersionedValue($collection, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\ValueNormalizer\CollectionNormalizer::supportsNormalization
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
            array(new Collection(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Collection(), 2), false),
            array(new VersionedValue(new Collection(), 1), true),
        );
    }
}
