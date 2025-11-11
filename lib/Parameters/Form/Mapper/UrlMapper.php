<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

final class UrlMapper extends Mapper
{
    public function getFormType(): string
    {
        return UrlType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'default_protocol' => null,
        ];
    }
}
