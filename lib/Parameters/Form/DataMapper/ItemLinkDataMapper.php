<?php

namespace Netgen\BlockManager\Parameters\Form\DataMapper;

use Symfony\Component\Form\DataMapperInterface;

class ItemLinkDataMapper implements DataMapperInterface
{
    /**
     * Maps properties of some data to a list of forms.
     *
     * @param mixed $data Structured data
     * @param \Symfony\Component\Form\FormInterface[] $forms A list of {@link FormInterface} instances
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException if the type of the data parameter is not supported
     */
    public function mapDataToForms($data, $forms)
    {
        $parsedData = parse_url($data);
        if (is_array($parsedData) && !empty($parsedData['scheme']) && !empty($parsedData['host'])) {
            $forms = iterator_to_array($forms);
            $forms['item_id']->setData($parsedData['host']);
            $forms['item_type']->setData(str_replace('-', '_', $parsedData['scheme']));
        }
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param \Symfony\Component\Form\FormInterface[] $forms A list of {@link FormInterface} instances
     * @param mixed $data Structured data
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException if the type of the data parameter is not supported
     */
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
