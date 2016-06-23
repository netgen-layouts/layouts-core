<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;

abstract class ParameterHandler implements ParameterHandlerInterface
{
    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function convertOptions(ParameterInterface $parameter)
    {
        return array();
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
        $constraints = $parameter->getConstraints();
        if ($options['parameter_validation_groups'] !== null) {
            foreach ($constraints as $constraint) {
                $constraint->groups = $options['parameter_validation_groups'];
            }
        }

        return array(
            'required' => $parameter->isRequired(),
            'label' => $options['label_prefix'] . '.' . $parameterName,
            'property_path' => $options['property_path_prefix'] . '[' . $parameterName . ']',
            'constraints' => $constraints,
        );
    }
}
