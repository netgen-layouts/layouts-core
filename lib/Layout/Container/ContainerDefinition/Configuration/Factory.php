<?php

namespace Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration;

class Factory
{
    /**
     * Builds the container definition configuration.
     *
     * @param string $identifier
     * @param array $config
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration
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

            $viewTypes[$viewTypeIdentifier] = new ViewType(
                array(
                    'identifier' => $viewTypeIdentifier,
                    'name' => $viewTypeConfig['name'],
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
