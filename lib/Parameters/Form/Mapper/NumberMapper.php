<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class NumberMapper extends Mapper
{
    public function getFormType()
    {
        return NumberType::class;
    }

    public function mapOptions(ParameterInterface $parameter)
    {
        return array(
            'scale' => $parameter->getOption('scale'),
        );
    }
}
