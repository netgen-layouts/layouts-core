<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\PluginRenderingRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PluginRenderingExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nglayouts_template_plugin',
                [PluginRenderingRuntime::class, 'renderPlugins'],
                [
                    'needs_context' => true,
                    'is_safe' => ['html'],
                ],
            ),
        ];
    }
}
