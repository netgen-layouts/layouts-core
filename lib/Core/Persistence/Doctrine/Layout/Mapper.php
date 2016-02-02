<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Block;

class Mapper
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

    /**
     * Maps data from database to block value objects.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block[]
     */
    public function mapBlocks(array $data = array())
    {
        $blocks = array();

        foreach ($data as $dataItem) {
            $parameters = !empty($dataItem['parameters']) ?
                json_decode($dataItem['parameters'], true) :
                array();

            $blocks[] = new Block(
                array(
                    'id' => (int)$dataItem['id'],
                    'layoutId' => (int)$dataItem['layout_id'],
                    'zoneIdentifier' => $dataItem['zone_identifier'],
                    'definitionIdentifier' => $dataItem['definition_identifier'],
                    'parameters' => is_array($parameters) ? $parameters : array(),
                    'viewType' => $dataItem['view_type'],
                    'name' => $dataItem['name'],
                    'status' => (int)$dataItem['status'],
                )
            );
        }

        return $blocks;
    }
}
