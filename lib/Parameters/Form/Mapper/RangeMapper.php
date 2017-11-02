<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

final class RangeMapper extends Mapper
{
    public function getFormType()
    {
        return RangeType::class;
    }

    public function mapOptions(ParameterInterface $parameter)
    {
        $options = $parameter->getOptions();

        return array(
            'attr' => array(
                'min' => $options['min'],
                'max' => $options['max'],
            ),
        );
    }
}
