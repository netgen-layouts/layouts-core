<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\AjaxRenderingRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AjaxRenderingExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'ngbm_ajax_block_pager',
                array(AjaxRenderingRuntime::class, 'renderAjaxBlockPager'),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_ajax_block_page_url',
                array(AjaxRenderingRuntime::class, 'getAjaxBlockPageUrl')
            ),
        );
    }
}
