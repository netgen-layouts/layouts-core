<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Handler\Plugin;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

final class HandlerPlugin extends Plugin
{
    /**
     * @var string[]
     */
    private static $extendedHandlers = array();

    /**
     * @param string[] $extendedHandlers
     *
     * @return \Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin
     */
    public static function instance(array $extendedHandlers = array())
    {
        self::$extendedHandlers = $extendedHandlers;

        return new self();
    }

    /**
     * Returns the fully qualified class name of the handler which this
     * plugin extends. If you wish to extend every existing handler,
     * return the FQCN of the block handler interface. You can also return
     * the list of FQCNs to make the plugin work on a set of blocks.
     *
     * @return string|string[]
     */
    public static function getExtendedHandler()
    {
        return self::$extendedHandlers;
    }

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add('test_param', ParameterType\TextLineType::class);
    }

    /**
     * Adds the dynamic parameters to the $params object for the provided block.
     *
     * @param \Netgen\BlockManager\Block\DynamicParameters $params
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     */
    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $params['dynamic_param'] = 'dynamic_value';
    }
}
