<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\Zone;

final class LayoutMapper
{
    /**
     * Maps data from database to layout values.
     *
     * @return \Netgen\Layouts\Persistence\Values\Layout\Layout[]
     */
    public function mapLayouts(array $data): array
    {
        $layouts = [];

        foreach ($data as $dataItem) {
            $layoutId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            if (!isset($layouts[$layoutId])) {
                $layouts[$layoutId] = [
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
                ];
            }

            $layouts[$layoutId]['availableLocales'][] = $locale;
        }

        return array_values(
            array_map(
                static function (array $layoutData): Layout {
                    if (isset($layoutData['availableLocales'])) {
                        sort($layoutData['availableLocales']);
                    }

                    return Layout::fromArray($layoutData);
                },
                $layouts
            )
        );
    }

    /**
     * Maps data from database to zone values.
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
                    'linkedLayoutId' => $dataItem['linked_layout_id'] !== null ? (int) $dataItem['linked_layout_id'] : null,
                    'linkedZoneIdentifier' => $dataItem['linked_zone_identifier'],
                ]
            );
        }

        return $zones;
    }
}
