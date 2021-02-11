<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Stubs;

use Netgen\Layouts\Parameters\Form\Mapper as BaseMapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\FormType;

final class FormMapper extends BaseMapper
{
    private bool $compound;

    public function __construct(bool $compound = false)
    {
        $this->compound = $compound;
    }

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
