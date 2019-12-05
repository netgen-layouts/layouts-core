<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Handler\Plugin;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;

final class HandlerPlugin extends Plugin
{
    /**
     * @var string[]
     */
    private static $extendedHandlers = [];

    /**
     * @param string[] $extendedHandlers
     */
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
