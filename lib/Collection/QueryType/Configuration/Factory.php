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
    public static function buildQueryTypeConfig($identifier, array $config)
    {
        $forms = array();

        foreach ($config['forms'] as $formIdentifier => $formConfig) {
            $forms[$formIdentifier] = new Form(
                $formIdentifier,
                $formConfig['type'],
                isset($formConfig['parameters']) ? $formConfig['parameters'] : array()
            );
        }

        return new Configuration($identifier, $config['name'], $forms);
    }
}
