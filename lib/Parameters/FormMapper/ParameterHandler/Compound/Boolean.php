<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound;

use Netgen\BlockManager\Parameters\FormMapper\CompoundParameterHandler;
use Netgen\BlockManager\Parameters\Form\CompoundBooleanType;
use Netgen\BlockManager\Parameters\ParameterInterface;

class Boolean extends CompoundParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    protected function getFormType()
    {
        return CompoundBooleanType::class;
    }

    /**
     * Returns default parameter options for Symfony form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(ParameterInterface $parameter, $parameterName, array $options)
    {
        return array(
            'checkbox_required' => $parameter->isRequired(),
            'checkbox_label' => $options['label_prefix'] . '.' . $parameterName,
            'checkbox_constraints' => $parameter->getConstraints(),
            'checkbox_property_path' => $options['property_path_prefix'] . '[' . $parameterName . ']',
        ) + parent::getDefaultOptions($parameter, $parameterName, $options);
    }
}
