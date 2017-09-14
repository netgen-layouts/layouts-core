<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper\Compound;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\ParameterInterface;

class BooleanMapper extends Mapper
{
    public function getFormType()
    {
        return CompoundBooleanType::class;
    }

    public function mapOptions(ParameterInterface $parameter)
    {
        return array(
            'mapped' => false,
            'reverse' => $parameter->getOption('reverse'),
        );
    }
}
