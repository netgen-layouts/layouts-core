<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Validator\Constraint\Parameters\Link as LinkConstraint;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class LinkType extends ParameterType
{
    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'link';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(array('value_types'));
        $optionsResolver->setAllowedTypes('value_types', 'array');
        $optionsResolver->setDefault('value_types', array());
    }

    /**
     * Converts the parameter value to from a domain format to scalar/hash format.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function toHash($value)
    {
        if (!$value instanceof LinkValue) {
            return null;
        }

        return array(
            'link_type' => $value->getLinkType(),
            'link' => $value->getLink(),
            'link_suffix' => $value->getLinkSuffix(),
            'new_window' => $value->getNewWindow(),
        );
    }

    /**
     * Converts the provided parameter value to value usable by the domain.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function fromHash($value)
    {
        if (!is_array($value) || empty($value['link_type'])) {
            return new LinkValue();
        }

        return new LinkValue(
            array(
                'linkType' => $value['link_type'],
                'link' => isset($value['link']) ? $value['link'] : null,
                'linkSuffix' => isset($value['link_suffix']) ? $value['link_suffix'] : null,
                'newWindow' => isset($value['new_window']) ? $value['new_window'] : false,
            )
        );
    }

    /**
     * Potentially converts the input value to value usable by the domain.
     *
     * If the value cannot be converted, original value should be returned.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function createValueFromInput($value)
    {
        if (!is_array($value) || empty($value['link_type'])) {
            return $value;
        }

        return $this->fromHash($value);
    }

    /**
     * Returns if the parameter value is empty.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty($value)
    {
        if (!$value instanceof LinkValue) {
            return true;
        }

        return empty($value->getLinkType()) || empty($value->getLink());
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        return array(
            new Constraints\Type(array('type' => LinkValue::class)),
            new LinkConstraint(
                array(
                    'required' => $parameter->isRequired(),
                    'valueTypes' => $parameter->getOptions()['value_types'],
                )
            ),
        );
    }
}
