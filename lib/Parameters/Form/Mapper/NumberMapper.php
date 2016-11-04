<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class NumberMapper extends Mapper
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return NumberType::class;
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
        $options = $parameter->getOptions();

        return array(
            'scale' => $options['scale'],
        );
    }
}
