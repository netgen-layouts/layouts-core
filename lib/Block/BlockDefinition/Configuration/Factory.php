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
        $viewTypes = array();

        foreach ($config['forms'] as $formIdentifier => $formConfig) {
            $forms[$formIdentifier] = new Form(
                $formIdentifier,
                $formConfig['type'],
                $formConfig['enabled'],
                isset($formConfig['parameters']) ? $formConfig['parameters'] : null
            );
        }

        foreach ($config['view_types'] as $viewTypeIdentifier => $viewTypeConfig) {
            if (!$viewTypeConfig['enabled']) {
                continue;
            }

            $itemViewTypes = array();
            foreach ($viewTypeConfig['item_view_types'] as $itemViewTypeIdentifier => $itemViewTypeConfig) {
                $itemViewTypes[$itemViewTypeIdentifier] = new ItemViewType(
                    $itemViewTypeIdentifier,
                    $itemViewTypeConfig['name']
                );
            }

            $viewTypes[$viewTypeIdentifier] = new ViewType(
                $viewTypeIdentifier,
                $viewTypeConfig['name'],
                $itemViewTypes
            );
        }

        return new Configuration($identifier, $forms, $viewTypes);
    }
}
