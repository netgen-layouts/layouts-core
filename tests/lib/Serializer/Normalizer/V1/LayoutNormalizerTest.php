<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Layout\Type\LayoutTypeFactory;
use Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class LayoutNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $blockServiceMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->normalizer = new LayoutNormalizer(
            $this->layoutServiceMock,
            $this->blockServiceMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::getAllowedBlocks
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::getZoneName
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::getZones
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::normalize
     */
    public function testNormalizeLayout()
    {
        $currentDate = new DateTimeImmutable();
        $currentDate->setTimestamp(time());

        $block = new Block(
            [
                'id' => 24,
            ]
        );

        $layoutType = LayoutTypeFactory::buildLayoutType(
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

        $layout = new Layout(
            [
                'id' => 42,
                'layoutType' => $layoutType,
                'status' => Value::STATUS_DRAFT,
                'created' => $currentDate,
                'modified' => $currentDate,
                'shared' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'zones' => new ArrayCollection(
                    [
                        'left' => new Zone(
                            [
                                'identifier' => 'left',
                                'linkedZone' => null,
                            ]
                        ),
                        'right' => new Zone(
                            [
                                'identifier' => 'right',
                                'linkedZone' => new Zone(
                                    [
                                        'layoutId' => 24,
                                        'identifier' => 'top',
                                    ]
                                ),
                            ]
                        ),
                        'missing' => new Zone(
                            [
                                'identifier' => 'missing',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->blockServiceMock
            ->expects($this->at(0))
            ->method('loadZoneBlocks')
            ->will($this->returnValue([$block]));

        $this->blockServiceMock
            ->expects($this->at(1))
            ->method('loadZoneBlocks')
            ->will($this->returnValue([]));

        $this->blockServiceMock
            ->expects($this->at(2))
            ->method('loadZoneBlocks')
            ->will($this->returnValue([]));

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('hasPublishedState')
            ->with($this->equalTo($layout))
            ->will($this->returnValue(true));

        $this->assertEquals(
            [
                'id' => $layout->getId(),
                'type' => $layoutType->getIdentifier(),
                'published' => false,
                'has_published_state' => true,
                'created_at' => $layout->getCreated()->format(DateTime::ISO8601),
                'updated_at' => $layout->getModified()->format(DateTime::ISO8601),
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
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\LayoutNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $this->assertEquals($expected, $this->normalizer->supportsNormalization($data));
    }

    /**
     * Provider for {@link self::testSupportsNormalization}.
     *
     * @return array
     */
    public function supportsNormalizationProvider()
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
