<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Handler;

use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;

use function array_flip;

/**
 * Block plugin which adds options to control AJAX paging of block collections.
 */
final class PagedCollectionsPlugin extends Plugin
{
    /**
     * @param string[] $pagerTypes
     * @param string[] $defaultGroups
     */
    public function __construct(
        /**
         * The list of pager types available in the plugin.
         *
         * Keys should be pager identifiers, and values should be human readable names.
         */
        private array $pagerTypes,
        private array $defaultGroups,
    ) {}

    public static function getExtendedHandlers(): iterable
    {
        yield PagedCollectionsBlockInterface::class;
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add(
            'paged_collections:enabled',
            ParameterType\Compound\BooleanType::class,
            [
                'label' => 'block.plugin.paged_collections.enabled',
                'groups' => $this->defaultGroups,
            ],
        );

        $builder->get('paged_collections:enabled')->add(
            'paged_collections:type',
            ParameterType\ChoiceType::class,
            [
                'options' => array_flip($this->pagerTypes),
                'label' => 'block.plugin.paged_collections.type',
                'groups' => $this->defaultGroups,
            ],
        );

        $builder->get('paged_collections:enabled')->add(
            'paged_collections:max_pages',
            ParameterType\IntegerType::class,
            [
                'min' => 1,
                'label' => 'block.plugin.paged_collections.max_pages',
                'groups' => $this->defaultGroups,
            ],
        );

        $builder->get('paged_collections:enabled')->add(
            'paged_collections:ajax_first',
            ParameterType\BooleanType::class,
            [
                'label' => 'block.plugin.paged_collections.ajax_first',
                'groups' => $this->defaultGroups,
            ],
        );
    }
}
