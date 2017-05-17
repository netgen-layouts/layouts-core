<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;

class LayoutMapper
{
    /**
     * Maps data from database to layout value objects.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout[]
     */
    public function mapLayouts(array $data = array())
    {
        $layouts = array();

        foreach ($data as $dataItem) {
            $layouts[] = new Layout(
                array(
                    'id' => (int) $dataItem['id'],
                    'type' => $dataItem['type'],
                    'name' => $dataItem['name'],
                    'description' => $dataItem['description'],
                    'created' => (int) $dataItem['created'],
                    'modified' => (int) $dataItem['modified'],
                    'status' => (int) $dataItem['status'],
                    'shared' => (bool) $dataItem['shared'],
                )
            );
        }

        return $layouts;
    }

    /**
     * Maps data from database to zone value objects.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone[]
     */
    public function mapZones(array $data = array())
    {
        $zones = array();

        foreach ($data as $dataItem) {
            $zones[] = new Zone(
                array(
                    'identifier' => $dataItem['identifier'],
                    'layoutId' => (int) $dataItem['layout_id'],
                    'status' => (int) $dataItem['status'],
                    'rootBlockId' => (int) $dataItem['root_block_id'],
                    'linkedLayoutId' => $dataItem['linked_layout_id'] !== null ? (int) $dataItem['linked_layout_id'] : null,
                    'linkedZoneIdentifier' => $dataItem['linked_zone_identifier'],
                )
            );
        }

        return $zones;
    }
}
