<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\QueryType\Form;
use Netgen\BlockManager\Configuration\QueryType\QueryType;

class QueryTypeFactory
{
    /**
     * Builds the query type configuration.
     *
     * @param array $config
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\QueryType\QueryType
     */
    public static function buildQueryType(array $config, $identifier)
    {
        $forms = array();

        foreach ($config['forms'] as $formIdentifier => $formConfig) {
            $forms[$formIdentifier] = new Form(
                $formIdentifier,
                $formConfig['type'],
                isset($formConfig['parameters']) ? $formConfig['parameters'] : array()
            );
        }

        return new QueryType($identifier, $forms);
    }
}
