<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionItemNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Collection\Item\VisibilityResolver;
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

#[CoversClass(CollectionItemNormalizer::class)]
final class CollectionItemNormalizerTest extends TestCase
{
    private MockObject&NormalizerInterface $normalizerMock;

    private MockObject&UrlGeneratorInterface $urlGeneratorMock;

    private CollectionItemNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->normalizer = new CollectionItemNormalizer($this->urlGeneratorMock, new VisibilityResolver([]));
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

    public function testNormalize(): void
    {
        $item = Item::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'position' => 3,
                'value' => 12,
                'viewType' => 'overlay',
                'definition' => ItemDefinition::fromArray(['valueType' => 'my_value_type']),
                'cmsItem' => CmsItem::fromArray(
                    [
                        'name' => 'Value name',
                        'isVisible' => true,
                        'value' => 12,
                        'valueType' => 'my_value_type',
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

        $this->urlGeneratorMock
            ->method('generate')
            ->with(self::identicalTo($item->cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/some/url');

        self::assertSame(
            [
                'id' => $item->id->toString(),
                'collection_id' => $item->collectionId->toString(),
                'position' => $item->position,
                'visible' => true,
                'value' => $item->value,
                'value_type' => $item->definition->getValueType(),
                'item_view_type' => $item->viewType,
                'name' => 'Value name',
                'cms_visible' => true,
                'cms_url' => '/some/url',
                'config' => $serializedConfig,
            ],
            $this->normalizer->normalize(new Value($item)),
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
            [new Item(), false],
            [new Value(new APIValue()), false],
            [new Value(new Item()), true],
        ];
    }
}
