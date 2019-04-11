<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\LazyCollection;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\Layouts\Layout\Type\NullLayoutType;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\Layouts\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\Layouts\Persistence\Values\Value as PersistenceValue;
use Netgen\Layouts\Utils\DateTimeUtils;

final class LayoutMapper
{
    /**
     * @var \Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\Layouts\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function __construct(LayoutHandlerInterface $layoutHandler, LayoutTypeRegistryInterface $layoutTypeRegistry)
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
            'layoutId' => $zone->layoutId,
            'status' => $zone->status,
            'linkedZone' => function () use ($zone): ?Zone {
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
                        function (PersistenceZone $zone): Zone {
                            return $this->mapZone($zone);
                        },
                        $this->layoutHandler->loadLayoutZones($layout)
                    );
                }
            ),
        ];

        return Layout::fromArray($layoutData);
    }
}
