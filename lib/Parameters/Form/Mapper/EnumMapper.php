<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper;

use BackedEnum;
use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

use function is_string;
use function sprintf;

final class EnumMapper extends Mapper
{
    public function getFormType(): string
    {
        return EnumType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        $options = [
            'class' => $parameterDefinition->getOption('class'),
            'multiple' => $parameterDefinition->getOption('multiple'),
            'expanded' => $parameterDefinition->getOption('expanded'),
        ];

        $optionLabelPrefix = $parameterDefinition->getOption('option_label_prefix');
        if (is_string($optionLabelPrefix)) {
            $options['choice_label'] = static fn (BackedEnum $enum): string => sprintf(
                '%s.%s',
                $optionLabelPrefix,
                $enum->value,
            );
        }

        return $options;
    }
}
