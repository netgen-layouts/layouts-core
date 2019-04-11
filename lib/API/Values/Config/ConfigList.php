<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Doctrine\Common\Collections\ArrayCollection;

final class ConfigList extends ArrayCollection
{
    public function __construct(array $configs = [])
    {
        parent::__construct(
            array_filter(
                $configs,
                static function (Config $config): bool {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\Config\Config[]
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
            static function (Config $config): string {
                return $config->getConfigKey();
            },
            $this->getConfigs()
        );
    }
}
