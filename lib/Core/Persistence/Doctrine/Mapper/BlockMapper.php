<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Values\Page\Block;

class BlockMapper
{
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
                    'position' => (int)$dataItem['position'],
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
