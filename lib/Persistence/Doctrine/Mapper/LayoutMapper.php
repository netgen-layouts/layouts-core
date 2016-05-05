<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;

class LayoutMapper
{
    /**
     * Maps data from database to layout value objects.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout[]
     */
    public function mapLayouts(array $data = array())
    {
        $layouts = array();

        foreach ($data as $dataItem) {
            $layouts[] = new Layout(
                array(
                    'id' => (int)$dataItem['id'],
                    'parentId' => $dataItem['parent_id'] !== null ? (int)$dataItem['parent_id'] : null,
                    'identifier' => $dataItem['identifier'],
                    'name' => $dataItem['name'],
                    'created' => (int)$dataItem['created'],
                    'modified' => (int)$dataItem['modified'],
                    'status' => (int)$dataItem['status'],
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
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone[]
     */
    public function mapZones(array $data = array())
    {
        $zones = array();

        foreach ($data as $dataItem) {
            $zones[] = new Zone(
                array(
                    'identifier' => $dataItem['identifier'],
                    'layoutId' => (int)$dataItem['layout_id'],
                    'status' => (int)$dataItem['status'],
                )
            );
        }

        return $zones;
    }
}
