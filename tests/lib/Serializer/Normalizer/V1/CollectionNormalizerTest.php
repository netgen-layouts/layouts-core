<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class CollectionNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizer = new CollectionNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => 42,
                'query' => new Query(),
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
            ]
        );

        $this->assertSame(
            [
                'id' => $collection->getId(),
                'type' => Collection::TYPE_DYNAMIC,
                'is_translatable' => $collection->isTranslatable(),
                'main_locale' => $collection->getMainLocale(),
                'always_available' => $collection->isAlwaysAvailable(),
                'available_locales' => $collection->getAvailableLocales(),
            ],
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
    public function testSupportsNormalization($data, bool $expected): void
    {
        $this->assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public function supportsNormalizationProvider(): array
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new Collection(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Collection(), 2), false],
            [new VersionedValue(new Collection(), 1), true],
        ];
    }
}
