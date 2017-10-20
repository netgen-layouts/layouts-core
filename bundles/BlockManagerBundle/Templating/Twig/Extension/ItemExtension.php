<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ItemExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'ngbm_item_path',
                array(ItemRuntime::class, 'getItemPath'),
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }
}
