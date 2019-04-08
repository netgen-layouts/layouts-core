<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Collection\Item\VisibilityResolver;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionItemNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $normalizerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGeneratorMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->normalizer = new CollectionItemNormalizer($this->urlGeneratorMock, new VisibilityResolver([]));
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $item = Item::fromArray(
            [
                'id' => 42,
                'collectionId' => 24,
                'position' => 3,
                'value' => 12,
                'definition' => ItemDefinition::fromArray(['valueType' => 'my_value_type']),
                'cmsItem' => CmsItem::fromArray(
                    [
                        'name' => 'Value name',
                        'isVisible' => true,
                        'value' => 12,
                        'valueType' => 'my_value_type',
                    ]
                ),
            ]
        );

        $serializedConfig = [
            'key' => [
                'param1' => 'value1',
                'param2' => 'value2',
            ],
        ];

        $this->normalizerMock
            ->expects(self::at(0))
            ->method('normalize')
            ->willReturn($serializedConfig);

        $this->urlGeneratorMock
            ->expects(self::any())
            ->method('generate')
            ->with(self::identicalTo($item->getCmsItem()))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => $item->getId(),
                'collection_id' => $item->getCollectionId(),
                'position' => $item->getPosition(),
                'visible' => true,
                'value' => $item->getValue(),
                'value_type' => $item->getDefinition()->getValueType(),
                'name' => 'Value name',
                'cms_visible' => true,
                'cms_url' => '/some/url',
                'config' => $serializedConfig,
            ],
            $this->normalizer->normalize(new VersionedValue($item, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::supportsNormalization
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
            [new Item(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Item(), 2), false],
            [new VersionedValue(new Item(), 1), true],
        ];
    }
}
