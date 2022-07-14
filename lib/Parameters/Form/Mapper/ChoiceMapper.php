<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use function is_callable;

final class ChoiceMapper extends Mapper
{
    public function getFormType(): string
    {
        return ChoiceType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        $options = $parameterDefinition->getOption('options');

        return [
            'multiple' => $parameterDefinition->getOption('multiple'),
            'expanded' => $parameterDefinition->getOption('expanded'),
            'choices' => is_callable($options) ? $options() : $options,
        ];
    }
}
