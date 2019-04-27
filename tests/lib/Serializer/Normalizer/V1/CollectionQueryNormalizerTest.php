<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Normalizer\V1;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Serializer\Normalizer\V1\CollectionQueryNormalizer;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionQueryNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $normalizerMock;

    /**
     * @var \Netgen\Layouts\Serializer\Normalizer\V1\CollectionQueryNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new CollectionQueryNormalizer();
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionQueryNormalizer::buildVersionedValues
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionQueryNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $query = Query::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'queryType' => new QueryType('my_query_type'),
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
                'parameters' => [
                    'param' => Parameter::fromArray(
                        [
                            'name' => 'param',
                            'value' => 'value',
                        ]
                    ),
                    'param2' => Parameter::fromArray(
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
            ->expects(self::once())
            ->method('normalize')
            ->willReturn($serializedParams);

        self::assertSame(
            [
                'id' => $query->getId()->toString(),
                'collection_id' => $query->getCollectionId()->toString(),
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
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionQueryNormalizer::supportsNormalization
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
            [new Query(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Query(), 2), false],
            [new VersionedValue(new Query(), 1), true],
        ];
    }
}
