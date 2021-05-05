<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ItemExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nglayouts_item_path',
                [ItemRuntime::class, 'getItemPath'],
                [
                    'is_safe' => ['html'],
                ],
            ),
        ];
    }
}
