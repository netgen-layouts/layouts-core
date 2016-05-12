<?php

namespace Netgen\BlockManager\Tests\Serializer\ValueNormalizer;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\ValueNormalizer\CollectionNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;

class CollectionNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\ValueNormalizer\CollectionNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new CollectionNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\ValueNormalizer\CollectionNormalizer::normalize
     */
    public function testNormalize()
    {
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
