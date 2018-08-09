<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Config;

use Doctrine\Common\Collections\ArrayCollection;

final class ConfigList extends ArrayCollection
{
    public function __construct(array $configs = [])
    {
        parent::__construct(
            array_filter(
                $configs,
                function (Config $config) {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function getConfigs(): array
    {
        return $this->toArray();
    }

    /**
     * @return string[]
     */
    public function getConfigKeys(): array
    {
        return array_map(
            function (Config $config) {
                return $config->getConfigKey();
            },
            $this->getConfigs()
        );
    }
}
