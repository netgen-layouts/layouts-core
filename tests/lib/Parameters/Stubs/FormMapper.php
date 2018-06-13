<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\Form\Mapper as BaseMapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\FormType;

final class FormMapper extends BaseMapper
{
    /**
     * @var bool
     */
    private $compound;

    public function __construct($compound = false)
    {
        $this->compound = $compound;
    }

    public function getFormType()
    {
        return FormType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition)
    {
        return [
            'compound' => $this->compound,
        ];
    }
}
