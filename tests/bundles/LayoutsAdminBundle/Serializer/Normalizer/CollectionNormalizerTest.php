<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CollectionNormalizer::class)]
final class CollectionNormalizerTest extends TestCase
{
    private CollectionNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new CollectionNormalizer();
    }

    public function testNormalize(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => Uuid::v7(),
                'query' => new Query(),
                'isTranslatable' => true,
                'isAlwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
            ],
        );

        self::assertSame(
            [
                'id' => $collection->id->toString(),
                'type' => $collection->collectionType->value,
                'is_translatable' => $collection->isTranslatable,
                'main_locale' => $collection->mainLocale,
                'always_available' => $collection->isAlwaysAvailable,
                'available_locales' => $collection->availableLocales,
            ],
            $this->normalizer->normalize(new Value($collection)),
        );
    }

    #[DataProvider('supportsNormalizationDataProvider')]
    public function testSupportsNormalization(mixed $data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    /**
     * @return iterable<mixed>
     */
    public static function supportsNormalizationDataProvider(): iterable
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new Collection(), false],
            [new Value(new APIValue()), false],
            [new Value(new Collection()), true],
        ];
    }
}
