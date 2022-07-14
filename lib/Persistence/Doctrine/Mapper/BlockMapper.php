<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Values\Block\Block;

use function array_map;
use function array_values;
use function is_array;
use function json_decode;
use function ksort;
use function sort;

final class BlockMapper
{
    /**
     * Maps data from database to block values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\Block\Block[]
     */
    public function mapBlocks(array $data, ?string $layoutUuid = null): array
    {
        $blocks = [];

        foreach ($data as $dataItem) {
            $blockId = (int) $dataItem['id'];
            $locale = $dataItem['locale'];

            $blocks[$blockId] ??= [
                'id' => $blockId,
                'uuid' => $dataItem['uuid'],
                'layoutId' => (int) $dataItem['layout_id'],
                'layoutUuid' => $layoutUuid ?? $dataItem['layout_uuid'] ?? '',
                'depth' => (int) $dataItem['depth'],
                'path' => $dataItem['path'],
                'parentId' => $dataItem['parent_id'] > 0 ? (int) $dataItem['parent_id'] : null,
                'parentUuid' => $dataItem['parent_uuid'] ?? null,
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
                'config' => $this->buildParameters((string) $dataItem['config']),
                'parameters' => [],
                'availableLocales' => [],
            ];

            $blocks[$blockId]['parameters'][$locale] = $this->buildParameters((string) $dataItem['parameters']);
            $blocks[$blockId]['availableLocales'][] = $locale;
        }

        return array_values(
            array_map(
                static function (array $blockData): Block {
                    ksort($blockData['parameters']);
                    sort($blockData['availableLocales']);

                    return Block::fromArray($blockData);
                },
                $blocks,
            ),
        );
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
