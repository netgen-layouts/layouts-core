<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use DateTimeImmutable;
use DateTimeInterface;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Type\LayoutTypeFactory;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Uid\Uuid;

#[CoversClass(LayoutNormalizer::class)]
final class LayoutNormalizerTest extends TestCase
{
    private Stub&LayoutService $layoutServiceStub;

    private Stub&BlockService $blockServiceStub;

    private LayoutTypeInterface $layoutType;

    private LayoutNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);
        $this->blockServiceStub = self::createStub(BlockService::class);

        $this->layoutType = LayoutTypeFactory::buildLayoutType(
            'test_layout_1',
            [
                'name' => 'Test layout 1',
                'icon' => '/icon.svg',
                'enabled' => true,
                'zones' => [
                    'left' => [
                        'name' => 'Left',
                        'allowed_block_definitions' => ['title'],
                    ],
                    'right' => [
                        'name' => 'Right',
                        'allowed_block_definitions' => [],
                    ],
                ],
            ],
        );

        $this->normalizer = new LayoutNormalizer($this->layoutServiceStub, $this->blockServiceStub);
        $this->normalizer->setNormalizer(new Serializer());
    }

    public function testNormalizeLayout(): void
    {
        $date1 = new DateTimeImmutable();
        $date1 = $date1->setTimestamp(123);

        $blockUuid = Uuid::v7();

        $block = Block::fromArray(
            [
                'id' => $blockUuid,
            ],
        );

        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();

        $layout = Layout::fromArray(
            [
                'id' => $uuid1,
                'layoutType' => $this->layoutType,
                'status' => Status::Draft,
                'created' => $date1,
                'modified' => $date1,
                'isShared' => true,
                'name' => 'My layout',
                'description' => 'My layout description',
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'zones' => ZoneList::fromArray(
                    [
                        'left' => Zone::fromArray(
                            [
                                'identifier' => 'left',
                                'linkedZone' => null,
                            ],
                        ),
                        'right' => Zone::fromArray(
                            [
                                'identifier' => 'right',
                                'linkedZone' => Zone::fromArray(
                                    [
                                        'layoutId' => $uuid2,
                                        'identifier' => 'top',
                                    ],
                                ),
                            ],
                        ),
                        'missing' => Zone::fromArray(
                            [
                                'identifier' => 'missing',
                                'linkedZone' => null,
                            ],
                        ),
                    ],
                ),
            ],
        );

        $this->blockServiceStub
            ->method('loadZoneBlocks')
            ->willReturnOnConsecutiveCalls(
                BlockList::fromArray([$block]),
                BlockList::fromArray([]),
                BlockList::fromArray([]),
            );

        $this->layoutServiceStub
            ->method('layoutExists')
            ->with(self::identicalTo($layout->id), self::identicalTo(Status::Published))
            ->willReturn(true);

        $this->layoutServiceStub
            ->method('loadLayoutArchive')
            ->with(self::identicalTo($layout->id))
            ->willThrowException(new NotFoundException('layout'));

        self::assertSame(
            [
                'id' => $layout->id->toString(),
                'type' => $this->layoutType->identifier,
                'published' => false,
                'has_published_state' => true,
                'created_at' => $layout->created->format(DateTimeInterface::ATOM),
                'updated_at' => $layout->modified->format(DateTimeInterface::ATOM),
                'has_archived_state' => false,
                'archive_created_at' => null,
                'archive_updated_at' => null,
                'shared' => true,
                'name' => $layout->name,
                'description' => $layout->description,
                'main_locale' => $layout->mainLocale,
                'available_locales' => [
                    'en' => 'English',
                    'hr' => 'Croatian',
                ],
                'zones' => [
                    [
                        'identifier' => 'left',
                        'name' => 'Left',
                        'block_ids' => [$blockUuid->toString()],
                        'allowed_block_definitions' => ['title'],
                        'linked_layout_id' => null,
                        'linked_zone_identifier' => null,
                    ],
                    [
                        'identifier' => 'right',
                        'name' => 'Right',
                        'block_ids' => [],
                        'allowed_block_definitions' => true,
                        'linked_layout_id' => $uuid2->toString(),
                        'linked_zone_identifier' => 'top',
                    ],
                    [
                        'identifier' => 'missing',
                        'name' => 'missing',
                        'block_ids' => [],
                        'allowed_block_definitions' => true,
                        'linked_layout_id' => null,
                        'linked_zone_identifier' => null,
                    ],
                ],
            ],
            $this->normalizer->normalize(new Value($layout)),
        );
    }

    public function testNormalizeLayoutWithArchivedLayout(): void
    {
        $date1 = new DateTimeImmutable();
        $date1 = $date1->setTimestamp(123);

        $date2 = new DateTimeImmutable();
        $date2 = $date2->setTimestamp(456);

        $uuid = Uuid::v7();

        $layout = Layout::fromArray(
            [
                'id' => $uuid,
                'layoutType' => $this->layoutType,
                'status' => Status::Published,
                'created' => $date1,
                'modified' => $date1,
                'isShared' => true,
                'name' => 'My layout',
                'description' => 'My layout description',
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'zones' => ZoneList::fromArray([]),
            ],
        );

        $archivedLayout = Layout::fromArray(
            [
                'id' => $uuid,
                'layoutType' => $this->layoutType,
                'status' => Status::Archived,
                'created' => $date2,
                'modified' => $date2,
                'isShared' => true,
                'name' => 'My layout',
                'description' => 'My layout description',
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'zones' => ZoneList::fromArray([]),
            ],
        );

        $this->layoutServiceStub
            ->method('layoutExists')
            ->with(self::identicalTo($layout->id), self::identicalTo(Status::Published))
            ->willReturn(true);

        $this->layoutServiceStub
            ->method('loadLayoutArchive')
            ->with(self::identicalTo($layout->id))
            ->willReturn($archivedLayout);

        $data = $this->normalizer->normalize(new Value($layout));

        self::assertTrue($data['has_archived_state']);
        self::assertSame($archivedLayout->created->format(DateTimeInterface::ATOM), $data['archive_created_at']);
        self::assertSame($archivedLayout->modified->format(DateTimeInterface::ATOM), $data['archive_updated_at']);
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
            ['layout', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new Layout(), false],
            [new Value(new APIValue()), false],
            [new Value(new Layout()), true],
        ];
    }
}
