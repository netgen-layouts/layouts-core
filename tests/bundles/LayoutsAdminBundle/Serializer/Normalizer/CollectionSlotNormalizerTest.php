<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionSlotNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CollectionSlotNormalizer::class)]
final class CollectionSlotNormalizerTest extends TestCase
{
    private CollectionSlotNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new CollectionSlotNormalizer();
    }

    public function testNormalize(): void
    {
        $slot = Slot::fromArray(
            [
                'id' => Uuid::v4(),
                'collectionId' => Uuid::v4(),
                'position' => 3,
                'viewType' => 'overlay',
            ],
        );

        self::assertSame(
            [
                'id' => $slot->id->toString(),
                'collection_id' => $slot->collectionId->toString(),
                'position' => $slot->position,
                'view_type' => $slot->viewType,
                'empty' => false,
            ],
            $this->normalizer->normalize(new Value($slot)),
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
            ['slot', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new Slot(), false],
            [new Value(new APIValue()), false],
            [new Value(new Slot()), true],
        ];
    }
}
