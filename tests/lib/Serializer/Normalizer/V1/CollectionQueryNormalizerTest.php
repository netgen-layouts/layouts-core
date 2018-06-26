<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionQueryNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionQueryNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $normalizerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionQueryNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new CollectionQueryNormalizer();
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer::setNormalizer
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionQueryNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $query = new Query(
            [
                'id' => 42,
                'collectionId' => 24,
                'queryType' => new QueryType('my_query_type'),
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
                'parameters' => [
                    'param' => new Parameter(
                        [
                            'name' => 'param',
                            'value' => 'value',
                        ]
                    ),
                    'param2' => new Parameter(
                        [
                            'name' => 'param2',
                            'value' => [
                                'param3' => 'value3',
                            ],
                        ]
                    ),
                ],
            ]
        );

        $serializedParams = [
            'param' => 'value',
            'param2' => [
                'param3' => 'value3',
            ],
        ];

        $this->normalizerMock
            ->expects($this->once())
            ->method('normalize')
            ->will($this->returnValue($serializedParams));

        $this->assertSame(
            [
                'id' => $query->getId(),
                'collection_id' => $query->getCollectionId(),
                'type' => $query->getQueryType()->getType(),
                'locale' => $query->getLocale(),
                'is_translatable' => $query->isTranslatable(),
                'always_available' => $query->isAlwaysAvailable(),
                'parameters' => $serializedParams,
            ],
            $this->normalizer->normalize(new VersionedValue($query, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionQueryNormalizer::supportsNormalization
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
            [new Query(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Query(), 2), false],
            [new VersionedValue(new Query(), 1), true],
        ];
    }
}
