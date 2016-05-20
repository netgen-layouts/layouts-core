<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition;
use Netgen\BlockManager\Configuration\BlockDefinition\ViewType;
use Netgen\BlockManager\Configuration\BlockDefinition\Form;

class BlockDefinitionFactory
{
    /**
     * Builds the block definition configuration.
     *
     * @param array $config
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition
     */
    public static function buildBlockDefinition(array $config, $identifier)
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

        return new BlockDefinition($identifier, $forms, $viewTypes);
    }
}
