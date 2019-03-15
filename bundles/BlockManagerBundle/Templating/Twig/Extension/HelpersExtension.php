<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class HelpersExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ngbm_layout_name',
                [HelpersRuntime::class, 'getLayoutName']
            ),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ngbm_locale_name',
                [HelpersRuntime::class, 'getLocaleName']
            ),
        ];
    }
}
