<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Stubs;

use Netgen\Layouts\Parameters\Form\Mapper as BaseMapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\FormType;

final class FormMapper extends BaseMapper
{
    public function __construct(
        private bool $compound = false,
    ) {}

    public function getFormType(): string
    {
        return FormType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'compound' => $this->compound,
        ];
    }
}
