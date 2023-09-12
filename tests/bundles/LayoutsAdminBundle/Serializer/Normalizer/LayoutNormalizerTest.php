<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Type\LayoutTypeFactory;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Serializer;

final class LayoutNormalizerTest extends TestCase
{
    private MockObject $layoutServiceMock;

    private MockObject $blockServiceMock;

    private LayoutTypeInterface $layoutType;

    private LayoutNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->layoutType = LayoutTypeFactory::buildLayoutType(
            '4_zones_a',
            [
                'name' => '4 zones A',
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

        $this->normalizer = new LayoutNormalizer($this->layoutServiceMock, $this->blockServiceMock);
        $this->normalizer->setNormalizer(new Serializer());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutNormalizer::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutNormalizer::getAllowedBlocks
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutNormalizer::getZoneName
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutNormalizer::getZones
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutNormalizer::normalize
     */
    public function testNormalizeLayout(): void
    {
        $date1 = new DateTimeImmutable();
        $date1 = $date1->setTimestamp(123);

        $blockUuid = Uuid::uuid4();

        $block = Block::fromArray(
            [
                'id' => $blockUuid,
            ],
        );

        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $layout = Layout::fromArray(
            [
                'id' => $uuid1,
                'layoutType' => $this->layoutType,
                'status' => APIValue::STATUS_DRAFT,
                'created' => $date1,
                'modified' => $date1,
                'shared' => true,
                'name' => 'My layout',
                'description' => 'My layout description',
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'zones' => new ArrayCollection(
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
                            ],
                        ),
                    ],
                ),
            ],
        );

        $this->blockServiceMock
            ->method('loadZoneBlocks')
            ->willReturnOnConsecutiveCalls(
                new BlockList([$block]),
                new BlockList(),
                new BlockList(),
            );

        $this->layoutServiceMock
            ->method('layoutExists')
            ->with(self::identicalTo($layout->getId()), self::identicalTo(Layout::STATUS_PUBLISHED))
            ->willReturn(true);

        $this->layoutServiceMock
            ->method('loadLayoutArchive')
            ->with(self::identicalTo($layout->getId()))
            ->willThrowException(new NotFoundException('layout'));

        self::assertSame(
            [
                'id' => $layout->getId()->toString(),
                'type' => $this->layoutType->getIdentifier(),
                'published' => false,
                'has_published_state' => true,
                'created_at' => $layout->getCreated()->format(DateTimeInterface::ATOM),
                'updated_at' => $layout->getModified()->format(DateTimeInterface::ATOM),
                'has_archived_state' => false,
                'archive_created_at' => null,
                'archive_updated_at' => null,
                'shared' => true,
                'name' => $layout->getName(),
                'description' => $layout->getDescription(),
                'main_locale' => $layout->getMainLocale(),
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

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutNormalizer::normalize
     */
    public function testNormalizeLayoutWithArchivedLayout(): void
    {
        $date1 = new DateTimeImmutable();
        $date1 = $date1->setTimestamp(123);

        $date2 = new DateTimeImmutable();
        $date2 = $date2->setTimestamp(456);

        $uuid = Uuid::uuid4();

        $layout = Layout::fromArray(
            [
                'id' => $uuid,
                'layoutType' => $this->layoutType,
                'status' => APIValue::STATUS_PUBLISHED,
                'created' => $date1,
                'modified' => $date1,
                'shared' => true,
                'name' => 'My layout',
                'description' => 'My layout description',
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'zones' => new ArrayCollection(),
            ],
        );

        $archivedLayout = Layout::fromArray(
            [
                'id' => $uuid,
                'layoutType' => $this->layoutType,
                'status' => APIValue::STATUS_ARCHIVED,
                'created' => $date2,
                'modified' => $date2,
                'shared' => true,
                'name' => 'My layout',
                'description' => 'My layout description',
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'zones' => new ArrayCollection(),
            ],
        );

        $this->layoutServiceMock
            ->method('layoutExists')
            ->with(self::identicalTo($layout->getId()), self::identicalTo(Layout::STATUS_PUBLISHED))
            ->willReturn(true);

        $this->layoutServiceMock
            ->method('loadLayoutArchive')
            ->with(self::identicalTo($layout->getId()))
            ->willReturn($archivedLayout);

        $data = $this->normalizer->normalize(new Value($layout));

        self::assertTrue($data['has_archived_state']);
        self::assertSame($archivedLayout->getCreated()->format(DateTimeInterface::ATOM), $data['archive_created_at']);
        self::assertSame($archivedLayout->getModified()->format(DateTimeInterface::ATOM), $data['archive_updated_at']);
    }

    /**
     * @param mixed $data
     *
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutNormalizer::supportsNormalization
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
