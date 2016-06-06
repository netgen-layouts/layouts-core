<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference;

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
                    'itemViewType' => $dataItem['item_view_type'],
                    'name' => $dataItem['name'],
                    'status' => (int)$dataItem['status'],
                )
            );
        }

        return $blocks;
    }

    /**
     * Maps data from database to collection reference value objects.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\CollectionReference[]
     */
    public function mapCollectionReferences(array $data = array())
    {
        $collectionReferences = array();

        foreach ($data as $dataItem) {
            $collectionReferences[] = new CollectionReference(
                array(
                    'blockId' => (int)$dataItem['block_id'],
                    'blockStatus' => (int)$dataItem['block_status'],
                    'collectionId' => (int)$dataItem['collection_id'],
                    'collectionStatus' => (int)$dataItem['collection_status'],
                    'identifier' => $dataItem['identifier'],
                    'offset' => (int)$dataItem['start'],
                    'limit' => $dataItem['length'] !== null ? (int)$dataItem['length'] : null,
                )
            );
        }

        return $collectionReferences;
    }
}
