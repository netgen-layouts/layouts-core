<?php

namespace Netgen\BlockManager\Tests\Configuration\Stubs;

use Netgen\BlockManager\Configuration\Configuration as BaseConfiguration;

class Configuration extends BaseConfiguration
{
    /**
     * Returns if parameter exists in configuration.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        true;
    }

    /**
     * Returns the parameter from configuration.
     *
     * @param string $parameterName
     *
     * @throws \InvalidArgumentException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if ($parameterName == 'blocks') {
            return array(
                'some_block' => array('name' => 'Some block'),
            );
        }

        if ($parameterName == 'block_types') {
            return array(
                'some_block_type' => array('type_name' => 'Some block type'),
            );
        }

        if ($parameterName == 'layouts') {
            return array(
                'some_layout' => array('name' => 'Some layout'),
            );
        }

        return array();
    }
}
