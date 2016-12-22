<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

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
            foreach ($viewTypeConfig['item_view_types'] as $itemViewTypeIdentifier => $itemViewTypeConfig) {
                $itemViewTypes[$itemViewTypeIdentifier] = new ItemViewType(
                    array(
                        'identifier' => $itemViewTypeIdentifier,
                        'name' => $itemViewTypeConfig['name'],
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
