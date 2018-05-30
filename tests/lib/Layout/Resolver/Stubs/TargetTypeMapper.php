<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

final class TargetTypeMapper implements MapperInterface
{
    public function getFormType()
    {
        return FormType::class;
    }

    public function getFormOptions()
    {
        return [];
    }

    public function handleForm(FormBuilderInterface $builder)
    {
    }
}
