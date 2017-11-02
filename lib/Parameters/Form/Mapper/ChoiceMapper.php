<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ChoiceMapper extends Mapper
{
    use ChoicesAsValuesTrait;

    public function getFormType()
    {
        return ChoiceType::class;
    }

    public function mapOptions(ParameterInterface $parameter)
    {
        $options = $parameter->getOptions();

        return array(
            'multiple' => $options['multiple'],
            'choices' => is_callable($options['options']) ?
                $options['options']() :
                $options['options'],
        ) + $this->getChoicesAsValuesOption();
    }
}
