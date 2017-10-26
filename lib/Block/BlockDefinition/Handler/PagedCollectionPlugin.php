<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

/**
 * Block plugin which adds options to control AJAX paging of block collections.
 */
class AjaxBlockPlugin extends Plugin
{
    public static function getExtendedHandler()
    {
        return BlockDefinitionHandlerInterface::class;
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }
}
