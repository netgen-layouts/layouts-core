<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Normalizer\V1;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultSetNormalizer;
use Netgen\Layouts\Serializer\Normalizer\ValueNormalizer;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Serializer\Stubs\NormalizerStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

final class CollectionResultSetNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultSetNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new CollectionResultSetNormalizer();
        $this->normalizer->setNormalizer(new Serializer([new ValueNormalizer(), new NormalizerStub()]));
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultSetNormalizer::buildVersionedValues
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultSetNormalizer::getOverflowItems
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultSetNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $item1 = Item::fromArray(['position' => 0]);
        $item2 = Item::fromArray(['position' => 1]);
        $item3 = Item::fromArray(['position' => 2]);
        $item4 = Item::fromArray(['position' => 3]);

        $result1 = new Result(0, new ManualItem($item2));
        $result2 = new Result(1, new ManualItem($item3));

        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(
                    [
                        'items' => new ArrayCollection([$item1, $item2, $item3, $item4]),
                    ]
                ),
                'results' => [$result1, $result2],
            ]
        );

        self::assertSame(
            [
                'items' => ['data', 'data'],
                'overflow_items' => ['data', 'data'],
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultSetNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
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
            [new ResultSet(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new ResultSet(), 2), false],
            [new VersionedValue(new ResultSet(), 1), true],
        ];
    }
}
