<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * Block plugin which adds options to control AJAX paging of block collections.
 */
class PagedCollectionsPlugin extends Plugin
{
    public static function getExtendedHandler()
    {
        return PagedCollectionsPluginInterface::class;
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'paged_collections:enabled',
            ParameterType\Compound\BooleanType::class,
            array(
                'label' => 'block.paged_collections.enabled',
                'groups' => array(
                    self::GROUP_DESIGN,
                ),
            )
        );

        $builder->get('paged_collections:enabled')->add(
            'paged_collections:type',
            ParameterType\ChoiceType::class,
            array(
                'options' => array(
                    'Pager' => 'pager',
                    'Load more' => 'load_more',
                ),
                'label' => 'block.paged_collections.type',
                'groups' => array(
                    self::GROUP_DESIGN,
                ),
            )
        );

        $builder->get('paged_collections:enabled')->add(
            'paged_collections:ajax_first',
            ParameterType\BooleanType::class,
            array(
                'label' => 'block.paged_collections.ajax_first',
                'groups' => array(
                    self::GROUP_DESIGN,
                ),
            )
        );
    }
}
