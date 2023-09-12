<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionQueryNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionQueryNormalizerTest extends TestCase
{
    private MockObject $normalizerMock;

    private CollectionQueryNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new CollectionQueryNormalizer();
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionQueryNormalizer::buildValues
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionQueryNormalizer::normalize
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
                        ],
                    ),
                    'param2' => Parameter::fromArray(
                        [
                            'name' => 'param2',
                            'value' => [
                                'param3' => 'value3',
                            ],
                        ],
                    ),
                ],
            ],
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
            $this->normalizer->normalize(new Value($query)),
        );
    }

    /**
     * @param mixed $data
     *
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionQueryNormalizer::supportsNormalization
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
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new Query(), false],
            [new Value(new APIValue()), false],
            [new Value(new Query()), true],
        ];
    }
}
