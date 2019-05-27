<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Normalizer\V1;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\Collection\Item\VisibilityResolver;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionResultNormalizerTest extends TestCase
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
     * @var \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->normalizer = new CollectionResultNormalizer($this->urlGeneratorMock, new VisibilityResolver([]));
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::__construct
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::buildVersionedValues
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalize(): void
    {
        $collectionItem = Item::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'viewType' => 'overlay',
                'cmsItem' => CmsItem::fromArray(
                    [
                        'name' => 'Value name',
                        'valueType' => 'value_type',
                        'isVisible' => true,
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

        $result = new Result(3, new ManualItem($collectionItem), null, Slot::fromArray(['viewType' => 'standard']));
        $this->urlGeneratorMock
            ->expects(self::any())
            ->method('generate')
            ->with(self::identicalTo($collectionItem->getCmsItem()))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => $collectionItem->getId()->toString(),
                'collection_id' => $collectionItem->getCollectionId()->toString(),
                'visible' => true,
                'is_dynamic' => false,
                'value' => $collectionItem->getCmsItem()->getValue(),
                'value_type' => $collectionItem->getCmsItem()->getValueType(),
                'item_view_type' => $collectionItem->getViewType(),
                'name' => $collectionItem->getCmsItem()->getName(),
                'cms_visible' => $collectionItem->getCmsItem()->isVisible(),
                'cms_url' => '/some/url',
                'config' => $serializedConfig,
                'position' => $result->getPosition(),
                'slot_view_type' => 'standard',
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::__construct
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::buildVersionedValues
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalizeWithoutSlot(): void
    {
        $collectionItem = Item::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'viewType' => 'overlay',
                'cmsItem' => CmsItem::fromArray(
                    [
                        'name' => 'Value name',
                        'valueType' => 'value_type',
                        'isVisible' => true,
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

        $result = new Result(3, new ManualItem($collectionItem));
        $this->urlGeneratorMock
            ->expects(self::any())
            ->method('generate')
            ->with(self::identicalTo($collectionItem->getCmsItem()))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => $collectionItem->getId()->toString(),
                'collection_id' => $collectionItem->getCollectionId()->toString(),
                'visible' => true,
                'is_dynamic' => false,
                'value' => $collectionItem->getCmsItem()->getValue(),
                'value_type' => $collectionItem->getCmsItem()->getValueType(),
                'item_view_type' => $collectionItem->getViewType(),
                'name' => $collectionItem->getCmsItem()->getName(),
                'cms_visible' => $collectionItem->getCmsItem()->isVisible(),
                'cms_url' => '/some/url',
                'config' => $serializedConfig,
                'position' => $result->getPosition(),
                'slot_view_type' => null,
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalizeWithoutCollectionItem(): void
    {
        $item = CmsItem::fromArray(
            [
                'name' => 'Value name',
                'valueType' => 'value_type',
                'isVisible' => true,
            ]
        );

        $result = new Result(3, $item);

        $this->normalizerMock
            ->expects(self::at(0))
            ->method('normalize')
            ->willReturn([]);

        $this->urlGeneratorMock
            ->expects(self::any())
            ->method('generate')
            ->with(self::identicalTo($item))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => null,
                'collection_id' => null,
                'visible' => true,
                'is_dynamic' => true,
                'value' => $item->getValue(),
                'value_type' => $item->getValueType(),
                'item_view_type' => null,
                'name' => $item->getName(),
                'cms_visible' => $item->isVisible(),
                'cms_url' => '/some/url',
                'config' => [],
                'position' => $result->getPosition(),
                'slot_view_type' => null,
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::normalize
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::normalizeResultItem
     */
    public function testNormalizeWithSubItem(): void
    {
        $item = CmsItem::fromArray(
            [
                'name' => 'Value name',
                'valueType' => 'value_type',
                'isVisible' => true,
            ]
        );

        $collectionItem = Item::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'viewType' => 'overlay',
                'cmsItem' => $item,
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
            ->willReturn([]);

        $this->normalizerMock
            ->expects(self::at(1))
            ->method('normalize')
            ->willReturn($serializedConfig);

        $result = new Result(3, new ManualItem($collectionItem), $item);
        $this->urlGeneratorMock
            ->expects(self::any())
            ->method('generate')
            ->with(self::identicalTo($collectionItem->getCmsItem()))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => null,
                'collection_id' => null,
                'visible' => true,
                'is_dynamic' => true,
                'value' => $item->getValue(),
                'value_type' => $item->getValueType(),
                'item_view_type' => null,
                'name' => $item->getName(),
                'cms_visible' => $item->isVisible(),
                'cms_url' => '/some/url',
                'config' => [],
                'position' => $result->getPosition(),
                'slot_view_type' => null,
                'override_item' => [
                    'id' => $collectionItem->getId()->toString(),
                    'collection_id' => $collectionItem->getCollectionId()->toString(),
                    'visible' => true,
                    'is_dynamic' => false,
                    'value' => $collectionItem->getCmsItem()->getValue(),
                    'value_type' => $collectionItem->getCmsItem()->getValueType(),
                    'item_view_type' => 'overlay',
                    'name' => $collectionItem->getCmsItem()->getName(),
                    'cms_visible' => $collectionItem->getCmsItem()->isVisible(),
                    'cms_url' => '/some/url',
                    'config' => $serializedConfig,
                ],
            ],
            $this->normalizer->normalize(new VersionedValue($result, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\CollectionResultNormalizer::supportsNormalization
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
            [new Result(0, new CmsItem()), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Result(0, new CmsItem()), 2), false],
            [new VersionedValue(new Result(0, new CmsItem()), 1), true],
        ];
    }
}
