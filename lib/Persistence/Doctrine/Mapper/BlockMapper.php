<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;

class BlockMapper
{
    /**
     * Maps data from database to block value objects.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function mapBlocks(array $data = array())
    {
        $blocks = array();

        foreach ($data as $dataItem) {
            $parameters = !empty($dataItem['parameters']) ?
                json_decode($dataItem['parameters'], true) :
                array();

            $config = !empty($dataItem['config']) ?
                json_decode($dataItem['config'], true) :
                array();

            $blocks[] = new Block(
                array(
                    'id' => (int) $dataItem['id'],
                    'layoutId' => (int) $dataItem['layout_id'],
                    'depth' => (int) $dataItem['depth'],
                    'path' => $dataItem['path'],
                    'parentId' => (int) $dataItem['parent_id'],
                    'placeholder' => $dataItem['placeholder'],
                    'position' => (int) $dataItem['position'],
                    'definitionIdentifier' => $dataItem['definition_identifier'],
                    'viewType' => $dataItem['view_type'],
                    'itemViewType' => $dataItem['item_view_type'],
                    'name' => $dataItem['name'],
                    'status' => (int) $dataItem['status'],
                    'parameters' => is_array($parameters) ? $parameters : array(),
                    'config' => is_array($config) ? $config : array(),
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
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference[]
     */
    public function mapCollectionReferences(array $data = array())
    {
        $collectionReferences = array();

        foreach ($data as $dataItem) {
            $collectionReferences[] = new CollectionReference(
                array(
                    'blockId' => (int) $dataItem['block_id'],
                    'blockStatus' => (int) $dataItem['block_status'],
                    'collectionId' => (int) $dataItem['collection_id'],
                    'collectionStatus' => (int) $dataItem['collection_status'],
                    'identifier' => $dataItem['identifier'],
                    'offset' => (int) $dataItem['start'],
                    'limit' => $dataItem['length'] !== null ? (int) $dataItem['length'] : null,
                )
            );
        }

        return $collectionReferences;
    }
}
