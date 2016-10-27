<?php

namespace Netgen\BlockManager\Parameters\Form\DataMapper;

use Symfony\Component\Form\DataMapperInterface;

class LinkDataMapper implements DataMapperInterface
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
        if (!empty($data['link_type'])) {
            $forms = iterator_to_array($forms);

            $forms['link_type']->setData($data['link_type']);
            $forms[$data['link_type']]->setData(isset($data['link']) ? $data['link'] : null);
            $forms['link_suffix']->setData(isset($data['link_suffix']) ? $data['link_suffix'] : null);
            $forms['new_window']->setData(isset($data['new_window']) ? $data['new_window'] : false);
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
        $linkType = $forms['link_type']->getData();

        $data = null;
        if (!empty($linkType)) {
            $data['link_type'] = $linkType;
            $data['link'] = null;
            $data['link_suffix'] = $forms['link_suffix']->getData();
            $data['new_window'] = $forms['new_window']->getData();

            if (isset($forms[$linkType])) {
                $data['link'] = $forms[$linkType]->getData();
            }
        }
    }
}
