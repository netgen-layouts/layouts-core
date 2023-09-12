<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionSlotNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class CollectionSlotNormalizerTest extends TestCase
{
    private CollectionSlotNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new CollectionSlotNormalizer();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionSlotNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $slot = Slot::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'position' => 3,
                'viewType' => 'overlay',
            ],
        );

        self::assertSame(
            [
                'id' => $slot->getId()->toString(),
                'collection_id' => $slot->getCollectionId()->toString(),
                'position' => $slot->getPosition(),
                'view_type' => $slot->getViewType(),
                'empty' => false,
            ],
            $this->normalizer->normalize(new Value($slot)),
        );
    }

    /**
     * @param mixed $data
     *
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionSlotNormalizer::supportsNormalization
     *
     * @dataProvider supportsNormalizationDataProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

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
