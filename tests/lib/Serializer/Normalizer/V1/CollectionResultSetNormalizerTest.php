<?php

declare(strict_types=1);

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

    public function setUp(): void
    {
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new CollectionResultSetNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultSetNormalizer::getOverflowItems
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionResultSetNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $result = new ResultSet(
            [
                'collection' => new Collection(
                    [
                        'items' => new ArrayCollection(
                            [
                                new Item(['position' => 0]),
                                new Item(['position' => 1]),
                                new Item(['position' => 2]),
                                new Item(['position' => 3]),
                            ]
                        ),
                    ]
                ),
                'results' => [
                    new Result(1, new ManualItem(new Item(['position' => 1]))),
                    new Result(2, new ManualItem(new Item(['position' => 2]))),
                ],
            ]
        );

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with(
                $this->equalTo(
                    [
                        new VersionedValue(new Result(1, new ManualItem(new Item(['position' => 1]))), 1),
                        new VersionedValue(new Result(2, new ManualItem(new Item(['position' => 2]))), 1),
                    ]
                )
            )
            ->will($this->returnValue(['items']));

        $this->serializerMock
            ->expects($this->at(1))
            ->method('normalize')
            ->with(
                $this->equalTo(
                    [
                        new VersionedValue(new Item(['position' => 0]), 1),
                        new VersionedValue(new Item(['position' => 3]), 1),
                    ]
                )
            )
            ->will($this->returnValue(['overflow_items']));

        $this->assertSame(
            [
                'items' => ['items'],
                'overflow_items' => ['overflow_items'],
            ],
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
            [new ResultSet(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new ResultSet(), 2), false],
            [new VersionedValue(new ResultSet(), 1), true],
        ];
    }
}
