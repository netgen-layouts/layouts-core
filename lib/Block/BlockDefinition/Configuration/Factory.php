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
    public static function buildBlockDefinitionConfig($identifier, array $config)
    {
        $forms = array();
        $viewTypes = array();

        foreach ($config['forms'] as $formIdentifier => $formConfig) {
            $forms[$formIdentifier] = new Form(
                $formIdentifier,
                $formConfig['type'],
                isset($formConfig['parameters']) ? $formConfig['parameters'] : array()
            );
        }

        foreach ($config['view_types'] as $viewTypeIdentifier => $viewTypeConfig) {
            $viewTypes[$viewTypeIdentifier] = new ViewType(
                $viewTypeIdentifier,
                $viewTypeConfig['name']
            );
        }

        return new Configuration($identifier, $forms, $viewTypes);
    }
}
