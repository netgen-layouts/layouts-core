<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;

final class CollectionMapper
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
            $collectionId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            if (!isset($collections[$collectionId])) {
                $collections[$collectionId] = array(
                    'id' => $collectionId,
                    'status' => (int) $dataItem['status'],
                    'offset' => (int) $dataItem['start'],
                    'limit' => $dataItem['length'] !== null ? (int) $dataItem['length'] : null,
                    'isTranslatable' => (bool) $dataItem['translatable'],
                    'mainLocale' => $dataItem['main_locale'],
                    'alwaysAvailable' => (bool) $dataItem['always_available'],
                );
            }

            $collections[$collectionId]['availableLocales'][] = $locale;
        }

        return array_values(
            array_map(
                function (array $collectionData) {
                    return new Collection($collectionData);
                },
                $collections
            )
        );
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
                    'id' => (int) $dataItem['id'],
                    'collectionId' => (int) $dataItem['collection_id'],
                    'position' => (int) $dataItem['position'],
                    'type' => (int) $dataItem['type'],
                    'valueId' => $dataItem['value_id'],
                    'valueType' => $dataItem['value_type'],
                    'status' => (int) $dataItem['status'],
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
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function mapQuery(array $data = array())
    {
        $queries = array();

        foreach ($data as $dataItem) {
            $queryId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            if (!isset($queries[$queryId])) {
                $queries[$queryId] = array(
                    'id' => $queryId,
                    'collectionId' => (int) $dataItem['collection_id'],
                    'type' => $dataItem['type'],
                    'status' => (int) $dataItem['status'],
                );
            }

            $queries[$queryId]['parameters'][$locale] = $this->buildParameters($dataItem['parameters']);
            $queries[$queryId]['availableLocales'][] = $locale;
        }

        $queries = array_values(
            array_map(
                function (array $queryData) {
                    return new Query($queryData);
                },
                $queries
            )
        );

        return reset($queries);
    }

    /**
     * Builds the array of parameters from provided JSON string.
     *
     * @param string $parameters
     *
     * @return array
     */
    private function buildParameters($parameters)
    {
        $parameters = !empty($parameters) ? json_decode($parameters, true) : array();

        return is_array($parameters) ? $parameters : array();
    }
}
