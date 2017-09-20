<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class CollectionNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->normalizer = new CollectionNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionNormalizer::normalize
     */
    public function testNormalize()
    {
        $collection = new Collection(
            array(
                'id' => 42,
                'type' => Collection::TYPE_DYNAMIC,
                'isTranslatable' => true,
                'availableLocales' => array('en'),
                'mainLocale' => 'en',
            )
        );

        $this->assertEquals(
            array(
                'id' => $collection->getId(),
                'type' => $collection->getType(),
                'is_translatable' => $collection->isTranslatable(),
                'main_locale' => $collection->getMainLocale(),
                'always_available' => $collection->isAlwaysAvailable(),
                'available_locales' => $collection->getAvailableLocales(),
            ),
            $this->normalizer->normalize(new VersionedValue($collection, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionNormalizer::supportsNormalization
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
            array(new Collection(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Collection(), 2), false),
            array(new VersionedValue(new Collection(), 1), true),
        );
    }
}
