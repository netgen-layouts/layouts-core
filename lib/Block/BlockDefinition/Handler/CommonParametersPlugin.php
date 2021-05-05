<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Handler;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;

/**
 * Block plugin which adds 3 parameters (css_class, css_id and set_container)
 * to every defined block.
 */
final class CommonParametersPlugin extends Plugin
{
    /**
     * @var string[]
     */
    private array $defaultGroups;

    /**
     * @param string[] $defaultGroups
     */
    public function __construct(array $defaultGroups)
    {
        $this->defaultGroups = $defaultGroups;
    }

    public static function getExtendedHandlers(): iterable
    {
        yield BlockDefinitionHandlerInterface::class;
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
            ],
        );

        $builder->add(
            'css_id',
            ParameterType\TextLineType::class,
            [
                'groups' => $this->defaultGroups,
                'label' => 'block.plugin.common_params.css_id',
                'translatable' => false,
            ],
        );

        $builder->add(
            'set_container',
            ParameterType\BooleanType::class,
            [
                'groups' => $this->defaultGroups,
                'label' => 'block.plugin.common_params.set_container',
                'translatable' => false,
            ],
        );
    }
}
