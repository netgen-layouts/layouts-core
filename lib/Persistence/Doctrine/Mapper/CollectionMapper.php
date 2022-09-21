<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Values\Block\CollectionReference;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\Query;
use Netgen\Layouts\Persistence\Values\Collection\Slot;

use function array_map;
use function array_values;
use function is_array;
use function json_decode;
use function ksort;
use function sort;

final class CollectionMapper
{
    /**
     * Maps data from database to collection values.
     *
     * If block UUID is provided, block UUID is mapped from it instead of data.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\Collection\Collection[]
     */
    public function mapCollections(array $data, ?string $blockUuid = null): array
    {
        $collections = [];

        foreach ($data as $dataItem) {
            $collectionId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            $collections[$collectionId] ??= [
                'id' => $collectionId,
                'uuid' => $dataItem['uuid'],
                'blockId' => (int) $dataItem['block_id'],
                'blockUuid' => $blockUuid ?? $dataItem['block_uuid'] ?? '',
                'status' => (int) $dataItem['status'],
                'offset' => (int) $dataItem['start'],
                'limit' => $dataItem['length'] !== null ? (int) $dataItem['length'] : null,
                'isTranslatable' => (bool) $dataItem['translatable'],
                'mainLocale' => $dataItem['main_locale'],
                'alwaysAvailable' => (bool) $dataItem['always_available'],
                'availableLocales' => [],
            ];

            $collections[$collectionId]['availableLocales'][] = $locale;
        }

        return array_values(
            array_map(
                static function (array $collectionData): Collection {
                    sort($collectionData['availableLocales']);

                    return Collection::fromArray($collectionData);
                },
                $collections,
            ),
        );
    }

    /**
     * Maps data from database to collection reference values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\Block\CollectionReference[]
     */
    public function mapCollectionReferences(array $data): array
    {
        $collectionReferences = [];

        foreach ($data as $dataItem) {
            $collectionReferences[] = CollectionReference::fromArray(
                [
                    'blockId' => (int) $dataItem['block_id'],
                    'blockStatus' => (int) $dataItem['block_status'],
                    'collectionId' => (int) $dataItem['collection_id'],
                    'collectionStatus' => (int) $dataItem['collection_status'],
                    'identifier' => $dataItem['identifier'],
                ],
            );
        }

        return $collectionReferences;
    }

    /**
     * Maps data from database to item values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\Collection\Item[]
     */
    public function mapItems(array $data): array
    {
        $items = [];

        foreach ($data as $dataItem) {
            $items[] = Item::fromArray(
                [
                    'id' => (int) $dataItem['id'],
                    'uuid' => $dataItem['uuid'],
                    'collectionId' => (int) $dataItem['collection_id'],
                    'collectionUuid' => $dataItem['collection_uuid'],
                    'position' => (int) $dataItem['position'],
                    'value' => $dataItem['value'],
                    'valueType' => $dataItem['value_type'],
                    'viewType' => $dataItem['view_type'],
                    'status' => (int) $dataItem['status'],
                    'config' => $this->buildParameters((string) $dataItem['config']),
                ],
            );
        }

        return $items;
    }

    /**
     * Maps data from database to query values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\Collection\Query[]
     */
    public function mapQueries(array $data): array
    {
        $queries = [];

        foreach ($data as $dataItem) {
            $queryId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            // isTranslatable, mainLocale and alwaysAvailable properties are
            // mapped after creating the query as they are taken from the collection
            $queries[$queryId] ??= [
                'id' => $queryId,
                'uuid' => $dataItem['uuid'],
                'collectionId' => (int) $dataItem['collection_id'],
                'collectionUuid' => $dataItem['collection_uuid'],
                'type' => $dataItem['type'],
                'status' => (int) $dataItem['status'],
                'availableLocales' => [],
                'parameters' => [],
            ];

            $queries[$queryId]['parameters'][$locale] = $this->buildParameters((string) $dataItem['parameters']);
            $queries[$queryId]['availableLocales'][] = $locale;
        }

        return array_values(
            array_map(
                static function (array $queryData): Query {
                    ksort($queryData['parameters']);
                    sort($queryData['availableLocales']);

                    return Query::fromArray($queryData);
                },
                $queries,
            ),
        );
    }

    /**
     * Maps data from database to slot values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\Collection\Slot[]
     */
    public function mapSlots(array $data): array
    {
        $slots = [];

        foreach ($data as $dataItem) {
            $position = (int) $dataItem['position'];
            $slots[$position] = Slot::fromArray(
                [
                    'id' => (int) $dataItem['id'],
                    'uuid' => $dataItem['uuid'],
                    'collectionId' => (int) $dataItem['collection_id'],
                    'collectionUuid' => $dataItem['collection_uuid'],
                    'position' => $position,
                    'viewType' => $dataItem['view_type'],
                    'status' => (int) $dataItem['status'],
                ],
            );
        }

        return $slots;
    }

    /**
     * Builds the array of parameters from provided JSON string.
     *
     * @return array<string, mixed>
     */
    private function buildParameters(string $parameters): array
    {
        $decodedParameters = json_decode($parameters, true);

        return is_array($decodedParameters) ? $decodedParameters : [];
    }
}
