<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType;

use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    public function getFormOptions()
    {
        return [];
    }

    public function handleForm(FormBuilderInterface $builder)
    {
    }
}
