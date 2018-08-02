<?php

declare(strict_types=1);

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
    private static $extendedHandlers = [];

    public static function instance(array $extendedHandlers): self
    {
        self::$extendedHandlers = $extendedHandlers;

        return new self();
    }

    public static function getExtendedHandlers(): array
    {
        return self::$extendedHandlers;
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add('test_param', ParameterType\TextLineType::class);
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block): void
    {
        $params['dynamic_param'] = 'dynamic_value';
    }
}
