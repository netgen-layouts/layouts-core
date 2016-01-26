<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Block;

use Netgen\BlockManager\Persistence\Values\Page\Block;

class Mapper
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
                    'zoneId' => (int)$dataItem['zone_id'],
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
