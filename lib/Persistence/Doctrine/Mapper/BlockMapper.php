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
            $blockId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            if (!isset($blocks[$blockId])) {
                $blocks[$blockId] = array(
                    'id' => $blockId,
                    'layoutId' => (int) $dataItem['layout_id'],
                    'depth' => (int) $dataItem['depth'],
                    'path' => $dataItem['path'],
                    'parentId' => $dataItem['parent_id'] > 0 ? (int) $dataItem['parent_id'] : null,
                    'placeholder' => $dataItem['placeholder'],
                    'position' => $dataItem['parent_id'] > 0 ? (int) $dataItem['position'] : null,
                    'definitionIdentifier' => $dataItem['definition_identifier'],
                    'viewType' => $dataItem['view_type'],
                    'itemViewType' => $dataItem['item_view_type'],
                    'name' => $dataItem['name'],
                    'isTranslatable' => (bool) $dataItem['translatable'],
                    'mainLocale' => $dataItem['main_locale'],
                    'alwaysAvailable' => (bool) $dataItem['always_available'],
                    'status' => (int) $dataItem['status'],
                    'config' => $this->buildParameters($dataItem['config']),
                );
            }

            $blocks[$blockId]['parameters'][$locale] = $this->buildParameters($dataItem['parameters']);
            $blocks[$blockId]['availableLocales'][] = $locale;
        }

        return array_values(
            array_map(
                function (array $blockData) {
                    return new Block($blockData);
                },
                $blocks
            )
        );
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

    /**
     * Builds the array of parameters from provided JSON string.
     *
     * @param string $parameters
     *
     * @return array
     */
    protected function buildParameters($parameters)
    {
        $parameters = !empty($parameters) ? json_decode($parameters, true) : array();

        return is_array($parameters) ? $parameters : array();
    }
}
