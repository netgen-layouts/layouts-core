<?php

namespace Netgen\BlockManager\Parameters\Form\Type\DataMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Symfony\Component\Form\DataMapperInterface;

/**
 * Mapper used to convert to and from the LinkValue object to the Symfony form structure.
 */
class LinkDataMapper implements DataMapperInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface
     */
    protected $parameter;

    public function __construct(ParameterInterface $parameter)
    {
        $this->parameter = $parameter;
    }

    public function mapDataToForms($data, $forms)
    {
        if (!$data instanceof LinkValue) {
            return;
        }

        $forms = iterator_to_array($forms);

        $forms['link_type']->setData($data->getLinkType());
        $forms['link_suffix']->setData($data->getLinkSuffix());
        $forms['new_window']->setData($data->getNewWindow());

        if (isset($forms[$data->getLinkType()])) {
            $forms[$data->getLinkType()]->setData($data->getLink());
        }
    }

    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);
        $linkType = $forms['link_type']->getData();

        $data = null;
        if (!empty($linkType)) {
            $data = array(
                'link_type' => $linkType,
                'link' => isset($forms[$linkType]) ? $forms[$linkType]->getData() : null,
                'link_suffix' => $forms['link_suffix']->getData(),
                'new_window' => $forms['new_window']->getData(),
            );
        }

        $data = $this->parameter->getType()->fromHash($this->parameter, $data);
    }
}
