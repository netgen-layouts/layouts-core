<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Exception\RuntimeException;

class Factory
{
    /**
     * Builds the block definition configuration.
     *
     * @param string $identifier
     * @param array $config
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public static function buildConfig($identifier, array $config)
    {
        $forms = array();
        $placeholderForms = array();
        $viewTypes = array();

        foreach ($config['forms'] as $formIdentifier => $formConfig) {
            if (!$formConfig['enabled']) {
                continue;
            }

            $forms[$formIdentifier] = new Form(
                array(
                    'identifier' => $formIdentifier,
                    'type' => $formConfig['type'],
                )
            );
        }

        foreach ($config['placeholder_forms'] as $formIdentifier => $formConfig) {
            if (!$formConfig['enabled']) {
                continue;
            }

            $placeholderForms[$formIdentifier] = new Form(
                array(
                    'identifier' => $formIdentifier,
                    'type' => $formConfig['type'],
                )
            );
        }

        foreach ($config['view_types'] as $viewTypeIdentifier => $viewTypeConfig) {
            if (!$viewTypeConfig['enabled']) {
                continue;
            }

            $itemViewTypes = array();

            if (!isset($viewTypeConfig['item_view_types']['standard'])) {
                $viewTypeConfig['item_view_types'] = array(
                    'standard' => array(
                        'name' => 'Standard',
                        'enabled' => true,
                    ),
                ) + $viewTypeConfig['item_view_types'];
            }

            foreach ($viewTypeConfig['item_view_types'] as $itemViewTypeIdentifier => $itemViewTypeConfig) {
                if (!$itemViewTypeConfig['enabled']) {
                    continue;
                }

                $itemViewTypes[$itemViewTypeIdentifier] = new ItemViewType(
                    array(
                        'identifier' => $itemViewTypeIdentifier,
                        'name' => $itemViewTypeConfig['name'],
                    )
                );
            }

            if (empty($itemViewTypes)) {
                throw new RuntimeException(
                    sprintf(
                        'You need to specify at least one enabled item view type for "%s" view type and "%s" block definition.',
                        $viewTypeIdentifier,
                        $identifier
                    )
                );
            }

            $viewTypes[$viewTypeIdentifier] = new ViewType(
                array(
                    'identifier' => $viewTypeIdentifier,
                    'name' => $viewTypeConfig['name'],
                    'itemViewTypes' => $itemViewTypes,
                    'validParameters' => $viewTypeConfig['valid_parameters'],
                )
            );
        }

        return new Configuration(
            array(
                'identifier' => $identifier,
                'name' => $config['name'],
                'forms' => $forms,
                'placeholderForms' => $placeholderForms,
                'viewTypes' => $viewTypes,
            )
        );
    }
}
