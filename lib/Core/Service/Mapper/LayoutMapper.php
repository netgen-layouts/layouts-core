<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Layout\Layout as APILayout;
use Netgen\BlockManager\API\Values\Layout\Zone as APIZone;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Core\Values\LazyCollection;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Layout\Type\NullLayoutType;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\BlockManager\Persistence\Values\Value as PersistenceValue;
use Netgen\BlockManager\Utils\DateTimeUtils;

final class LayoutMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function __construct(LayoutHandlerInterface $layoutHandler, LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        $this->layoutHandler = $layoutHandler;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Builds the API zone value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function mapZone(PersistenceZone $zone): APIZone
    {
        $zoneData = [
            'identifier' => $zone->identifier,
            'layoutId' => $zone->layoutId,
            'status' => $zone->status,
            'linkedZone' => function () use ($zone): ?APIZone {
                if ($zone->linkedLayoutId === null || $zone->linkedZoneIdentifier === null) {
                    return null;
                }

                try {
                    // We're always using published versions of linked zones
                    $linkedZone = $this->layoutHandler->loadZone(
                        $zone->linkedLayoutId,
                        PersistenceValue::STATUS_PUBLISHED,
                        $zone->linkedZoneIdentifier
                    );

                    return $this->mapZone($linkedZone);
                } catch (NotFoundException $e) {
                    return null;
                }
            },
        ];

        return new Zone($zoneData);
    }

    /**
     * Builds the API layout value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function mapLayout(PersistenceLayout $layout): APILayout
    {
        try {
            $layoutType = $this->layoutTypeRegistry->getLayoutType($layout->type);
        } catch (LayoutTypeException $e) {
            $layoutType = new NullLayoutType($layout->type);
        }

        $layoutData = [
            'id' => $layout->id,
            'layoutType' => $layoutType,
            'name' => $layout->name,
            'description' => $layout->description,
            'created' => DateTimeUtils::createFromTimestamp($layout->created),
            'modified' => DateTimeUtils::createFromTimestamp($layout->modified),
            'status' => $layout->status,
            'shared' => $layout->shared,
            'mainLocale' => $layout->mainLocale,
            'availableLocales' => $layout->availableLocales,
            'zones' => new LazyCollection(
                function () use ($layout): array {
                    return array_map(
                        function (PersistenceZone $zone): APIZone {
                            return $this->mapZone($zone);
                        },
                        $this->layoutHandler->loadLayoutZones($layout)
                    );
                }
            ),
        ];

        return new Layout($layoutData);
    }
}
