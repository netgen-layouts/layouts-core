<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * Block plugin which adds 3 parameters (css_class, css_id and set_container)
 * to every defined block.
 */
final class CommonParametersPlugin extends Plugin
{
    /**
     * @var array
     */
    private $defaultGroups;

    public function __construct(array $defaultGroups)
    {
        $this->defaultGroups = $defaultGroups;
    }

    public static function getExtendedHandlers(): array
    {
        return [BlockDefinitionHandlerInterface::class];
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add(
            'css_class',
            ParameterType\TextLineType::class,
            [
                'groups' => $this->defaultGroups,
                'label' => 'block.plugin.common_params.css_class',
                'translatable' => false,
            ]
        );

        $builder->add(
            'css_id',
            ParameterType\TextLineType::class,
            [
                'groups' => $this->defaultGroups,
                'label' => 'block.plugin.common_params.css_id',
                'translatable' => false,
            ]
        );

        $builder->add(
            'set_container',
            ParameterType\BooleanType::class,
            [
                'groups' => $this->defaultGroups,
                'label' => 'block.plugin.common_params.set_container',
                'translatable' => false,
            ]
        );
    }
}
