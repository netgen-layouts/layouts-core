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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CollectionResultNormalizer::class)]
final class CollectionResultNormalizerTest extends TestCase
{
    private Stub&NormalizerInterface $normalizerStub;

    private Stub&UrlGeneratorInterface $urlGeneratorStub;

    private CollectionResultNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizerStub = self::createStub(NormalizerInterface::class);
        $this->urlGeneratorStub = self::createStub(UrlGeneratorInterface::class);

        $this->normalizer = new CollectionResultNormalizer($this->urlGeneratorStub, new VisibilityResolver([]));
        $this->normalizer->setNormalizer($this->normalizerStub);
    }

    public function testNormalize(): void
    {
        $collectionItem = Item::fromArray(
            [
                'id' => Uuid::v4(),
                'collectionId' => Uuid::v4(),
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

        $this->normalizerStub
            ->method('normalize')
            ->willReturn($serializedConfig);

        $slotUuid = Uuid::v4();
        $result = Result::fromArray(
            [
                'position' => 3,
                'item' => new ManualItem($collectionItem),
                'subItem' => null,
                'slot' => Slot::fromArray(['id' => $slotUuid, 'viewType' => 'standard']),
            ],
        );

        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($collectionItem->cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => $collectionItem->id->toString(),
                'collection_id' => $collectionItem->collectionId->toString(),
                'visible' => true,
                'is_dynamic' => false,
                'value' => $collectionItem->cmsItem->value,
                'value_type' => $collectionItem->cmsItem->valueType,
                'item_view_type' => $collectionItem->viewType,
                'name' => $collectionItem->cmsItem->name,
                'cms_visible' => $collectionItem->cmsItem->isVisible,
                'cms_url' => '/some/url',
                'config' => $serializedConfig,
                'position' => $result->position,
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
                'id' => Uuid::v4(),
                'collectionId' => Uuid::v4(),
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

        $this->normalizerStub
            ->method('normalize')
            ->willReturn($serializedConfig);

        $result = Result::fromArray(['position' => 3, 'item' => new ManualItem($collectionItem)]);
        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($collectionItem->cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => $collectionItem->id->toString(),
                'collection_id' => $collectionItem->collectionId->toString(),
                'visible' => true,
                'is_dynamic' => false,
                'value' => $collectionItem->cmsItem->value,
                'value_type' => $collectionItem->cmsItem->valueType,
                'item_view_type' => $collectionItem->viewType,
                'name' => $collectionItem->cmsItem->name,
                'cms_visible' => $collectionItem->cmsItem->isVisible,
                'cms_url' => '/some/url',
                'config' => $serializedConfig,
                'position' => $result->position,
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

        $result = Result::fromArray(['position' => 3, 'item' => $item]);

        $this->normalizerStub
            ->method('normalize')
            ->willReturn([]);

        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($item), self::identicalTo(UrlType::Admin))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => null,
                'collection_id' => null,
                'visible' => true,
                'is_dynamic' => true,
                'value' => $item->value,
                'value_type' => $item->valueType,
                'item_view_type' => null,
                'name' => $item->name,
                'cms_visible' => $item->isVisible,
                'cms_url' => '/some/url',
                'config' => [],
                'position' => $result->position,
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
                'id' => Uuid::v4(),
                'collectionId' => Uuid::v4(),
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

        $this->normalizerStub
            ->method('normalize')
            ->willReturnOnConsecutiveCalls(
                [],
                $serializedConfig,
            );

        $result = Result::fromArray(['position' => 3, 'item' => new ManualItem($collectionItem), 'subItem' => $item]);
        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($collectionItem->cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => null,
                'collection_id' => null,
                'visible' => true,
                'is_dynamic' => true,
                'value' => $item->value,
                'value_type' => $item->valueType,
                'item_view_type' => null,
                'name' => $item->name,
                'cms_visible' => $item->isVisible,
                'cms_url' => '/some/url',
                'config' => [],
                'position' => $result->position,
                'slot_id' => null,
                'slot_view_type' => null,
                'override_item' => [
                    'id' => $collectionItem->id->toString(),
                    'collection_id' => $collectionItem->collectionId->toString(),
                    'visible' => true,
                    'is_dynamic' => false,
                    'value' => $collectionItem->cmsItem->value,
                    'value_type' => $collectionItem->cmsItem->valueType,
                    'item_view_type' => 'overlay',
                    'name' => $collectionItem->cmsItem->name,
                    'cms_visible' => $collectionItem->cmsItem->isVisible,
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
            [Result::fromArray(['position' => 0, 'item' => new CmsItem()]), false],
            [new Value(new APIValue()), false],
            [new Value(Result::fromArray(['position' => 0, 'item' => new CmsItem()])), true],
        ];
    }
}
