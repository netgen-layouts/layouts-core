<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPagerRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CollectionPagerExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nglayouts_collection_pager',
                [CollectionPagerRuntime::class, 'renderCollectionPager'],
                [
                    'is_safe' => ['html'],
                ],
            ),
            new TwigFunction(
                'nglayouts_collection_page_url',
                [CollectionPagerRuntime::class, 'getCollectionPageUrl'],
            ),
        ];
    }
}
