<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

final class EnumMapper extends Mapper
{
    public function getFormType(): string
    {
        return EnumType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'class' => $parameterDefinition->getOption('class'),
            'multiple' => $parameterDefinition->getOption('multiple'),
            'expanded' => $parameterDefinition->getOption('expanded'),
        ];
    }
}
