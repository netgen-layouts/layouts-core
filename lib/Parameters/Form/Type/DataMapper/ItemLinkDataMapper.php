<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Form\Type\DataMapper;

use Symfony\Component\Form\DataMapperInterface;

/**
 * Mapper used to convert to and from the "link" to an item in "value_type://value"
 * format to the Symfony form structure.
 */
final class ItemLinkDataMapper implements DataMapperInterface
{
    public function mapDataToForms($data, $forms): void
    {
        if (!is_string($data)) {
            return;
        }

        $parsedData = parse_url($data);
        if (is_array($parsedData) && !empty($parsedData['scheme']) && isset($parsedData['host'])) {
            $forms = iterator_to_array($forms);
            $forms['item_value']->setData($parsedData['host']);
            $forms['item_type']->setData(str_replace('-', '_', $parsedData['scheme']));
        }
    }

    public function mapFormsToData($forms, &$data): void
    {
        $forms = iterator_to_array($forms);

        $itemValue = $forms['item_value']->getData();
        $itemType = $forms['item_type']->getData();

        $data = null;
        if (!empty($itemValue) && !empty($itemType)) {
            $data = str_replace('_', '-', $itemType) . '://' . $itemValue;
        }
    }
}
