<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class HelpersExtension extends AbstractExtension
{
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
