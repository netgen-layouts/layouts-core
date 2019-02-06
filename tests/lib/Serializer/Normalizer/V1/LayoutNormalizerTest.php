<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockList;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Type\LayoutTypeFactory;
use Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\TestCase\LegacyTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

final class LayoutNormalizerTest extends TestCase
{
    use LegacyTestCaseTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $blockServiceMock;

    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutTypeInterface
     */
    private $layoutType;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer
     */
    private $normalizer;

    public function setUp(): void
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
            ]
        );

        $this->normalizer = new LayoutNormalizer($this->layoutServiceMock, $this->blockServiceMock);
        $this->normalizer->setSerializer(new Serializer());
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::getAllowedBlocks
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::getZoneName
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::getZones
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::normalize
     */
    public function testNormalizeLayout(): void
    {
        $date1 = new DateTimeImmutable();
        $date1 = $date1->setTimestamp(123);

        $block = Block::fromArray(
            [
                'id' => 24,
            ]
        );

        $layout = Layout::fromArray(
            [
                'id' => 42,
                'layoutType' => $this->layoutType,
                'status' => Value::STATUS_DRAFT,
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
                            ]
                        ),
                        'right' => Zone::fromArray(
                            [
                                'identifier' => 'right',
                                'linkedZone' => Zone::fromArray(
                                    [
                                        'layoutId' => 24,
                                        'identifier' => 'top',
                                    ]
                                ),
                            ]
                        ),
                        'missing' => Zone::fromArray(
                            [
                                'identifier' => 'missing',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->blockServiceMock
            ->expects(self::at(0))
            ->method('loadZoneBlocks')
            ->will(self::returnValue(new BlockList([$block])));

        $this->blockServiceMock
            ->expects(self::at(1))
            ->method('loadZoneBlocks')
            ->will(self::returnValue(new BlockList()));

        $this->blockServiceMock
            ->expects(self::at(2))
            ->method('loadZoneBlocks')
            ->will(self::returnValue(new BlockList()));

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('hasStatus')
            ->with(self::identicalTo($layout->getId()), self::identicalTo(Layout::STATUS_PUBLISHED))
            ->will(self::returnValue(true));

        $this->layoutServiceMock
            ->expects(self::at(1))
            ->method('loadLayoutArchive')
            ->with(self::identicalTo($layout->getId()))
            ->will(self::throwException(new NotFoundException('layout')));

        self::assertSame(
            [
                'id' => $layout->getId(),
                'type' => $this->layoutType->getIdentifier(),
                'published' => false,
                'has_published_state' => true,
                'created_at' => $layout->getCreated()->format(DateTime::ISO8601),
                'updated_at' => $layout->getModified()->format(DateTime::ISO8601),
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
                        'block_ids' => [24],
                        'allowed_block_definitions' => ['title'],
                        'linked_layout_id' => null,
                        'linked_zone_identifier' => null,
                    ],
                    [
                        'identifier' => 'right',
                        'name' => 'Right',
                        'block_ids' => [],
                        'allowed_block_definitions' => true,
                        'linked_layout_id' => 24,
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
            $this->normalizer->normalize(new VersionedValue($layout, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::normalize
     */
    public function testNormalizeLayoutWithArchivedLayout(): void
    {
        $date1 = new DateTimeImmutable();
        $date1 = $date1->setTimestamp(123);

        $date2 = new DateTimeImmutable();
        $date2 = $date2->setTimestamp(456);

        $layout = Layout::fromArray(
            [
                'id' => 42,
                'layoutType' => $this->layoutType,
                'status' => Value::STATUS_PUBLISHED,
                'created' => $date1,
                'modified' => $date1,
                'shared' => true,
                'name' => 'My layout',
                'description' => 'My layout description',
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'zones' => new ArrayCollection(),
            ]
        );

        $archivedLayout = Layout::fromArray(
            [
                'id' => 42,
                'layoutType' => $this->layoutType,
                'status' => Value::STATUS_ARCHIVED,
                'created' => $date2,
                'modified' => $date2,
                'shared' => true,
                'name' => 'My layout',
                'description' => 'My layout description',
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'zones' => new ArrayCollection(),
            ]
        );

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('hasStatus')
            ->with(self::identicalTo($layout->getId()), self::identicalTo(Layout::STATUS_PUBLISHED))
            ->will(self::returnValue(true));

        $this->layoutServiceMock
            ->expects(self::at(1))
            ->method('loadLayoutArchive')
            ->with(self::identicalTo($layout->getId()))
            ->will(self::returnValue($archivedLayout));

        $data = $this->normalizer->normalize(new VersionedValue($layout, 1));

        self::assertIsArray($data);
        self::assertTrue($data['has_archived_state']);
        self::assertSame($archivedLayout->getCreated()->format(DateTime::ISO8601), $data['archive_created_at']);
        self::assertSame($archivedLayout->getModified()->format(DateTime::ISO8601), $data['archive_updated_at']);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::supportsNormalization
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
            ['layout', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new Layout(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Layout(), 2), false],
            [new VersionedValue(new Layout(), 1), true],
        ];
    }
}
