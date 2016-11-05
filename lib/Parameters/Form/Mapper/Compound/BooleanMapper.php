<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper\Compound;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\ParameterInterface;

class BooleanMapper extends Mapper
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return CompoundBooleanType::class;
    }

    /**
     * Maps parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param array $formOptions
     *
     * @return array
     */
    public function mapOptions(ParameterInterface $parameter, $parameterName, array $formOptions)
    {
        return array(
            'mapped' => false,

            'reverse' => $parameter->getOptions()['reverse'],
        );
    }
}
