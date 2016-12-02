<?php

namespace Netgen\BlockManager\Parameters\Form\Type\DataMapper;

use Netgen\BlockManager\Parameters\ParameterTypeInterface;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Symfony\Component\Form\DataMapperInterface;

class LinkDataMapper implements DataMapperInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    protected $parameterType;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $parameterType
     */
    public function __construct(ParameterTypeInterface $parameterType)
    {
        $this->parameterType = $parameterType;
    }

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
            $data = array(
                'link_type' => $linkType,
                'link' => isset($forms[$linkType]) ? $forms[$linkType]->getData() : null,
                'link_suffix' => $forms['link_suffix']->getData(),
                'new_window' => $forms['new_window']->getData(),
            );
        }

        $data = $this->parameterType->fromHash($data);
    }
}
