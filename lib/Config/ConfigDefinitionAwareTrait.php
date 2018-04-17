<?php

namespace Netgen\BlockManager\Config;

trait ConfigDefinitionAwareTrait
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    protected $configDefinitions = [];

    public function getConfigDefinitions()
    {
        return $this->configDefinitions;
    }
}
