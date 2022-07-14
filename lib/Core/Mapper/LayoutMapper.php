<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\LazyCollection;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Layout\Type\NullLayoutType;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\Layouts\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\Layouts\Persistence\Values\Value as PersistenceValue;
use Netgen\Layouts\Utils\DateTimeUtils;
use Ramsey\Uuid\Uuid;

use function array_map;

final class LayoutMapper
{
    private LayoutHandlerInterface $layoutHandler;

    private LayoutTypeRegistry $layoutTypeRegistry;

    public function __construct(LayoutHandlerInterface $layoutHandler, LayoutTypeRegistry $layoutTypeRegistry)
    {
        $this->layoutHandler = $layoutHandler;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Builds the API zone value from persistence one.
     */
    public function mapZone(PersistenceZone $zone): Zone
    {
        $zoneData = [
            'identifier' => $zone->identifier,
            'layoutId' => Uuid::fromString($zone->layoutUuid),
            'status' => $zone->status,
            'linkedZone' => function () use ($zone): ?Zone {
                if ($zone->linkedLayoutUuid === null || $zone->linkedZoneIdentifier === null) {
                    return null;
                }

                try {
                    // We're always using published versions of linked zones
                    $linkedZone = $this->layoutHandler->loadZone(
                        $zone->linkedLayoutUuid,
                        PersistenceValue::STATUS_PUBLISHED,
                        $zone->linkedZoneIdentifier,
                    );

                    return $this->mapZone($linkedZone);
                } catch (NotFoundException $e) {
                    return null;
                }
            },
        ];

        return Zone::fromArray($zoneData);
    }

    /**
     * Builds the API layout value from persistence one.
     */
    public function mapLayout(PersistenceLayout $layout): Layout
    {
        try {
            $layoutType = $this->layoutTypeRegistry->getLayoutType($layout->type);
        } catch (LayoutTypeException $e) {
            $layoutType = new NullLayoutType($layout->type);
        }

        $layoutData = [
            'id' => Uuid::fromString($layout->uuid),
            'layoutType' => $layoutType,
            'name' => $layout->name,
            'description' => $layout->description,
            'created' => DateTimeUtils::create($layout->created),
            'modified' => DateTimeUtils::create($layout->modified),
            'status' => $layout->status,
            'shared' => $layout->shared,
            'mainLocale' => $layout->mainLocale,
            'availableLocales' => $layout->availableLocales,
            'zones' => new LazyCollection(
                fn (): array => array_map(
                    fn (PersistenceZone $zone): Zone => $this->mapZone($zone),
                    $this->layoutHandler->loadLayoutZones($layout),
                ),
            ),
        ];

        return Layout::fromArray($layoutData);
    }
}
