<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper\Compound;

use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType;
use Netgen\Layouts\Parameters\ParameterDefinition;

final class BooleanMapper extends Mapper
{
    public function getFormType(): string
    {
        return CompoundBooleanType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'mapped' => false,
            'reverse' => $parameterDefinition->getOption('reverse'),
        ];
    }
}
