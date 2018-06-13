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
    public function mapDataToForms($data, $forms)
    {
        if (!is_string($data)) {
            return;
        }

        $parsedData = parse_url($data);
        if (is_array($parsedData) && !empty($parsedData['scheme']) && (!empty($parsedData['host']) || $parsedData['host'] === '0')) {
            $forms = iterator_to_array($forms);
            $forms['item_id']->setData($parsedData['host']);
            $forms['item_type']->setData(str_replace('-', '_', $parsedData['scheme']));
        }
    }

    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        $itemId = $forms['item_id']->getData();
        $itemType = $forms['item_type']->getData();

        $data = null;
        if (!empty($itemId) && !empty($itemType)) {
            $data = str_replace('_', '-', $itemType) . '://' . $itemId;
        }
    }
}
