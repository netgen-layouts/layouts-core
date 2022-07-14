<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Doctrine\Common\Collections\ArrayCollection;

use function array_filter;
use function array_map;
use function array_values;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<string, \Netgen\Layouts\API\Values\Config\Config>
 */
final class ConfigList extends ArrayCollection
{
    /**
     * @param array<string, \Netgen\Layouts\API\Values\Config\Config> $configs
     */
    public function __construct(array $configs = [])
    {
        parent::__construct(
            array_filter(
                $configs,
                static fn (Config $config): bool => true,
            ),
        );
    }

    /**
     * @return array<string, \Netgen\Layouts\API\Values\Config\Config>
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
        return array_values(
            array_map(
                static fn (Config $config): string => $config->getConfigKey(),
                $this->getConfigs(),
            ),
        );
    }
}
