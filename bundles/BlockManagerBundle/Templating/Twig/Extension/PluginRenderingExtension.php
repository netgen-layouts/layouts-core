<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\PluginRenderingRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PluginRenderingExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ngbm_template_plugin',
                [PluginRenderingRuntime::class, 'renderPlugins'],
                [
                    'needs_context' => true,
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }
}
