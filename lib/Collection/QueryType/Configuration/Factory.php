<?php

namespace Netgen\BlockManager\Collection\QueryType\Configuration;

class Factory
{
    /**
     * Builds the query type configuration.
     *
     * @param string $identifier
     * @param array $config
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    public static function buildConfig($identifier, array $config)
    {
        $forms = array();

        foreach ($config['forms'] as $formIdentifier => $formConfig) {
            $forms[$formIdentifier] = new Form(
                $formIdentifier,
                $formConfig['type'],
                isset($formConfig['parameters']) ? $formConfig['parameters'] : null
            );
        }

        return new Configuration($identifier, $config['name'], $forms, $config['defaults']);
    }
}
