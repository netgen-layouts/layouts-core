<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Form\TargetType;

use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    public function getFormOptions(): array
    {
        return [];
    }

    public function handleForm(FormBuilderInterface $builder): void {}
}
