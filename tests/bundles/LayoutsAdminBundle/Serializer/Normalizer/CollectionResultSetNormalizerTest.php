<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ArrayValueNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionResultSetNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs\NormalizerStub;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemList;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

#[CoversClass(CollectionResultSetNormalizer::class)]
final class CollectionResultSetNormalizerTest extends TestCase
{
    private CollectionResultSetNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new CollectionResultSetNormalizer();
        $this->normalizer->setNormalizer(new Serializer([new ArrayValueNormalizer(), new NormalizerStub()]));
    }

    public function testNormalize(): void
    {
        $item1 = Item::fromArray(['position' => 0]);
        $item2 = Item::fromArray(['position' => 1]);
        $item3 = Item::fromArray(['position' => 2]);
        $item4 = Item::fromArray(['position' => 3]);

        $result1 = Result::fromArray(['position' => 0, 'item' => new ManualItem($item2)]);
        $result2 = Result::fromArray(['position' => 1, 'item' => new ManualItem($item3)]);

        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(
                    [
                        'items' => ItemList::fromArray([$item1, $item2, $item3, $item4]),
                    ],
                ),
                'results' => [$result1, $result2],
            ],
        );

        self::assertSame(
            [
                'items' => ['data', 'data'],
                'overflow_items' => ['data', 'data'],
            ],
            $this->normalizer->normalize(new Value($result)),
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
            [new ResultSet(), false],
            [new Value(new APIValue()), false],
            [new Value(new ResultSet()), true],
        ];
    }
}
