<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Form\Mapper\Compound;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\ParameterDefinition;

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
