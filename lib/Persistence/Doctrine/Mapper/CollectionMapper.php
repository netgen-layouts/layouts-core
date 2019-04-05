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
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection[]
     */
    public function mapCollections(array $data): array
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
                static function (array $collectionData): Collection {
                    sort($collectionData['availableLocales']);

                    return Collection::fromArray($collectionData);
                },
                $collections
            )
        );
    }

    /**
     * Maps data from database to item values.
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item[]
     */
    public function mapItems(array $data): array
    {
        $items = [];

        foreach ($data as $dataItem) {
            $items[] = Item::fromArray(
                [
                    'id' => (int) $dataItem['id'],
                    'collectionId' => (int) $dataItem['collection_id'],
                    'position' => (int) $dataItem['position'],
                    'value' => $dataItem['value'],
                    'valueType' => $dataItem['value_type'],
                    'status' => (int) $dataItem['status'],
                    'config' => $this->buildParameters((string) $dataItem['config']),
                ]
            );
        }

        return $items;
    }

    /**
     * Maps data from database to query values.
     */
    public function mapQueries(array $data): array
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

            $queries[$queryId]['parameters'][$locale] = $this->buildParameters((string) $dataItem['parameters']);
            $queries[$queryId]['availableLocales'][] = $locale;
        }

        $queries = array_values(
            array_map(
                static function (array $queryData): Query {
                    ksort($queryData['parameters']);
                    sort($queryData['availableLocales']);

                    return Query::fromArray($queryData);
                },
                $queries
            )
        );

        return $queries;
    }

    /**
     * Builds the array of parameters from provided JSON string.
     */
    private function buildParameters(string $parameters): array
    {
        $decodedParameters = json_decode($parameters, true);

        return is_array($decodedParameters) ? $decodedParameters : [];
    }
}
