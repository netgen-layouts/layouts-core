<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;

class Boolean extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    protected function getFormType()
    {
        return 'ngbm_compound_boolean';
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function convertOptions(ParameterInterface $parameter)
    {
        return array(
            'parameters' => $parameter->getParameters(),
        );
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
            'label' => false,
            'checkbox_required' => $parameter->isRequired(),
            'checkbox_label' => $options['label_prefix'] . '.' . $parameterName,
            'checkbox_constraints' => $parameter->getConstraints($options['validation_groups']),
            'checkbox_property_path' => $this->getPropertyPath(
                $options['property_path_prefix'],
                $parameterName
            ),
            'form_mapper_options' => $options,
        );
    }
}
