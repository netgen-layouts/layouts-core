<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ItemExtension extends AbstractExtension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return \Twig\TwigFunction[]
     */
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
