<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CollectionPagerExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'ngbm_collection_pager',
                array(CollectionPagerRuntime::class, 'renderCollectionPager'),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_collection_page_url',
                array(CollectionPagerRuntime::class, 'getCollectionPageUrl')
            ),
        );
    }
}
