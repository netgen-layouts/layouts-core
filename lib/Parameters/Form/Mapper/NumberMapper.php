<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class NumberMapper extends Mapper
{
    public function getFormType(): string
    {
        return NumberType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'scale' => $parameterDefinition->getOption('scale'),
        ];
    }
}
