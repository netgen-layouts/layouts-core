<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RenderingExtension extends AbstractExtension
{
    public function getName()
    {
        return self::class;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'ngbm_render_item',
                array(RenderingRuntime::class, 'renderItem'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_render_layout',
                array(RenderingRuntime::class, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_render_parameter',
                array(RenderingRuntime::class, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_render_block',
                array(RenderingRuntime::class, 'renderBlock'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_render_placeholder',
                array(RenderingRuntime::class, 'renderPlaceholder'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_render_rule',
                array(RenderingRuntime::class, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_render_rule_target',
                array(RenderingRuntime::class, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_render_rule_condition',
                array(RenderingRuntime::class, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new TwigFunction(
                'ngbm_render_value_object',
                array(RenderingRuntime::class, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function getTokenParsers()
    {
        return array(new RenderZone());
    }
}
