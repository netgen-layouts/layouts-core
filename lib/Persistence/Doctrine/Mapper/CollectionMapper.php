<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;

final class CollectionMapper
{
    /**
     * Maps data from database to collection values.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection[]
     */
    public function mapCollections(array $data = [])
    {
        $collections = [];

        foreach ($data as $dataItem) {
            $collectionId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            if (!isset($collections[$collectionId])) {
                $collections[$collectionId] = [
                    'id' => $collectionId,
                    'status' => (int) $dataItem['status'],
                    'offset' => (int) $dataItem['start'],
                    'limit' => $dataItem['length'] !== null ? (int) $dataItem['length'] : null,
                    'isTranslatable' => (bool) $dataItem['translatable'],
                    'mainLocale' => $dataItem['main_locale'],
                    'alwaysAvailable' => (bool) $dataItem['always_available'],
                ];
            }

            $collections[$collectionId]['availableLocales'][] = $locale;
        }

        return array_values(
            array_map(
                function (array $collectionData): Collection {
                    sort($collectionData['availableLocales']);

                    return new Collection($collectionData);
                },
                $collections
            )
        );
    }

    /**
     * Maps data from database to item values.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item[]
     */
    public function mapItems(array $data = [])
    {
        $items = [];

        foreach ($data as $dataItem) {
            $items[] = new Item(
                [
                    'id' => (int) $dataItem['id'],
                    'collectionId' => (int) $dataItem['collection_id'],
                    'position' => (int) $dataItem['position'],
                    'type' => (int) $dataItem['type'],
                    'value' => $dataItem['value'],
                    'valueType' => $dataItem['value_type'],
                    'status' => (int) $dataItem['status'],
                    'config' => $this->buildParameters($dataItem['config']),
                ]
            );
        }

        return $items;
    }

    /**
     * Maps data from database to query values.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function mapQuery(array $data = [])
    {
        $queries = [];

        foreach ($data as $dataItem) {
            $queryId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            if (!isset($queries[$queryId])) {
                $queries[$queryId] = [
                    'id' => $queryId,
                    'collectionId' => (int) $dataItem['collection_id'],
                    'type' => $dataItem['type'],
                    'status' => (int) $dataItem['status'],
                ];
            }

            $queries[$queryId]['parameters'][$locale] = $this->buildParameters($dataItem['parameters']);
            $queries[$queryId]['availableLocales'][] = $locale;
        }

        $queries = array_values(
            array_map(
                function (array $queryData): Query {
                    ksort($queryData['parameters']);
                    sort($queryData['availableLocales']);

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
        $parameters = !empty($parameters) ? json_decode($parameters, true) : [];

        return is_array($parameters) ? $parameters : [];
    }
}
