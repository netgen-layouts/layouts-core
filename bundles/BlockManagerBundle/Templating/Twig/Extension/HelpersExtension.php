<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class HelpersExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter(
                'ngbm_locale_name',
                array(HelpersRuntime::class, 'getLocaleName')
            ),
        );
    }
}
