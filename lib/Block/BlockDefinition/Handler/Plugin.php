<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Handler;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;

abstract class Plugin implements PluginInterface
{
    /**
     * @return iterable<string>
     */
    public static function getExtendedIdentifiers(): iterable
    {
        return [];
    }

    public static function getExtendedHandlers(): iterable
    {
        return [];
    }

    public function buildParameters(ParameterBuilderInterface $builder): void {}

    public function getDynamicParameters(DynamicParameters $params, Block $block): void {}
}
