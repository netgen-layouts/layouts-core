<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\Form\UriType;

class Uri extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return UriType::class;
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
            'label' => false,
        );
    }
}
