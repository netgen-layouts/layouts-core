<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionResultNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\Collection\Item\VisibilityResolver;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Netgen\Layouts\Item\UrlType;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[CoversClass(CollectionResultNormalizer::class)]
final class CollectionResultNormalizerTest extends TestCase
{
    private MockObject&NormalizerInterface $normalizerMock;

    private MockObject&UrlGeneratorInterface $urlGeneratorMock;

    private CollectionResultNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->normalizer = new CollectionResultNormalizer($this->urlGeneratorMock, new VisibilityResolver([]));
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

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
                        'value' => 42,
                        'valueType' => 'value_type',
                        'isVisible' => true,
                    ],
                ),
            ],
        );

        $serializedConfig = [
            'key' => [
                'param1' => 'value1',
                'param2' => 'value2',
            ],
        ];

        $this->normalizerMock
            ->method('normalize')
            ->willReturn($serializedConfig);

        $slotUuid = Uuid::uuid4();
        $result = new Result(3, new ManualItem($collectionItem), null, Slot::fromArray(['id' => $slotUuid, 'viewType' => 'standard']));
        $this->urlGeneratorMock
            ->method('generate')
            ->with(self::identicalTo($collectionItem->getCmsItem()), self::identicalTo(UrlType::Admin))
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
                'slot_id' => $slotUuid->toString(),
                'slot_view_type' => 'standard',
            ],
            $this->normalizer->normalize(new Value($result)),
        );
    }

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
                        'value' => 42,
                        'valueType' => 'value_type',
                        'isVisible' => true,
                    ],
                ),
            ],
        );

        $serializedConfig = [
            'key' => [
                'param1' => 'value1',
                'param2' => 'value2',
            ],
        ];

        $this->normalizerMock
            ->method('normalize')
            ->willReturn($serializedConfig);

        $result = new Result(3, new ManualItem($collectionItem));
        $this->urlGeneratorMock
            ->method('generate')
            ->with(self::identicalTo($collectionItem->getCmsItem()), self::identicalTo(UrlType::Admin))
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
                'slot_id' => null,
                'slot_view_type' => null,
            ],
            $this->normalizer->normalize(new Value($result)),
        );
    }

    public function testNormalizeWithoutCollectionItem(): void
    {
        $item = CmsItem::fromArray(
            [
                'name' => 'Value name',
                'value' => 42,
                'valueType' => 'value_type',
                'isVisible' => true,
            ],
        );

        $result = new Result(3, $item);

        $this->normalizerMock
            ->method('normalize')
            ->willReturn([]);

        $this->urlGeneratorMock
            ->method('generate')
            ->with(self::identicalTo($item), self::identicalTo(UrlType::Admin))
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
                'slot_id' => null,
                'slot_view_type' => null,
            ],
            $this->normalizer->normalize(new Value($result)),
        );
    }

    public function testNormalizeWithSubItem(): void
    {
        $item = CmsItem::fromArray(
            [
                'name' => 'Value name',
                'value' => 42,
                'valueType' => 'value_type',
                'isVisible' => true,
            ],
        );

        $collectionItem = Item::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'viewType' => 'overlay',
                'cmsItem' => $item,
            ],
        );

        $serializedConfig = [
            'key' => [
                'param1' => 'value1',
                'param2' => 'value2',
            ],
        ];

        $this->normalizerMock
            ->method('normalize')
            ->willReturnOnConsecutiveCalls(
                [],
                $serializedConfig,
            );

        $result = new Result(3, new ManualItem($collectionItem), $item);
        $this->urlGeneratorMock
            ->method('generate')
            ->with(self::identicalTo($collectionItem->getCmsItem()), self::identicalTo(UrlType::Admin))
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
                'slot_id' => null,
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
            $this->normalizer->normalize(new Value($result)),
        );
    }

    #[DataProvider('supportsNormalizationDataProvider')]
    public function testSupportsNormalization(mixed $data, bool $expected): void
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
            [new Result(0, new CmsItem()), false],
            [new Value(new APIValue()), false],
            [new Value(new Result(0, new CmsItem())), true],
        ];
    }
}
