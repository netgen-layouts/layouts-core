<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;

class Mapper
{
    /**
     * Maps data from database to collection value objects.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection[]
     */
    public function mapCollections(array $data = array())
    {
        $collections = array();

        foreach ($data as $dataItem) {
            $collections[] = new Collection(
                array(
                    'id' => (int)$dataItem['id'],
                    'type' => (int)$dataItem['type'],
                    'name' => $dataItem['name'],
                    'status' => (int)$dataItem['status'],
                )
            );
        }

        return $collections;
    }

    /**
     * Maps data from database to item value objects.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item[]
     */
    public function mapItems(array $data = array())
    {
        $items = array();

        foreach ($data as $dataItem) {
            $items[] = new Item(
                array(
                    'id' => (int)$dataItem['id'],
                    'collectionId' => (int)$dataItem['collection_id'],
                    'position' => (int)$dataItem['position'],
                    'linkType' => (int)$dataItem['link_type'],
                    'valueId' => $dataItem['value_id'],
                    'valueType' => $dataItem['value_type'],
                    'status' => (int)$dataItem['status'],
                )
            );
        }

        return $items;
    }

    /**
     * Maps data from database to query value objects.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query[]
     */
    public function mapQueries(array $data = array())
    {
        $queries = array();

        foreach ($data as $dataItem) {
            $parameters = !empty($dataItem['parameters']) ?
                json_decode($dataItem['parameters'], true) :
                array();

            $queries[] = new Query(
                array(
                    'id' => (int)$dataItem['id'],
                    'collectionId' => (int)$dataItem['collection_id'],
                    'identifier' => $dataItem['identifier'],
                    'type' => $dataItem['type'],
                    'parameters' => is_array($parameters) ? $parameters : array(),
                    'status' => (int)$dataItem['status'],
                )
            );
        }

        return $queries;
    }
}
