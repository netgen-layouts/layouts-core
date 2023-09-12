<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [];
    }

    public function handleForm(FormBuilderInterface $form, ParameterDefinition $parameterDefinition): void {}
}
