<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\Zone;

use function array_map;
use function array_values;
use function sort;

final class LayoutMapper
{
    /**
     * Maps data from database to layout values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\Layout\Layout[]
     */
    public function mapLayouts(array $data): array
    {
        $layouts = [];

        foreach ($data as $dataItem) {
            $layoutId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            $layouts[$layoutId] ??= [
                'id' => $layoutId,
                'uuid' => $dataItem['uuid'],
                'type' => $dataItem['type'],
                'name' => $dataItem['name'],
                'description' => $dataItem['description'],
                'created' => (int) $dataItem['created'],
                'modified' => (int) $dataItem['modified'],
                'status' => (int) $dataItem['status'],
                'shared' => (bool) $dataItem['shared'],
                'mainLocale' => $dataItem['main_locale'],
                'availableLocales' => [],
            ];

            $layouts[$layoutId]['availableLocales'][] = $locale;
        }

        return array_values(
            array_map(
                static function (array $layoutData): Layout {
                    sort($layoutData['availableLocales']);

                    return Layout::fromArray($layoutData);
                },
                $layouts,
            ),
        );
    }

    /**
     * Maps data from database to zone values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\Layout\Zone[]
     */
    public function mapZones(array $data): array
    {
        $zones = [];

        foreach ($data as $dataItem) {
            $zones[$dataItem['identifier']] = Zone::fromArray(
                [
                    'identifier' => $dataItem['identifier'],
                    'layoutId' => (int) $dataItem['layout_id'],
                    'layoutUuid' => $dataItem['layout_uuid'],
                    'status' => (int) $dataItem['status'],
                    'rootBlockId' => (int) $dataItem['root_block_id'],
                    'linkedLayoutUuid' => $dataItem['linked_layout_uuid'],
                    'linkedZoneIdentifier' => $dataItem['linked_zone_identifier'],
                ],
            );
        }

        return $zones;
    }
}
