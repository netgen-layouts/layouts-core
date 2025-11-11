<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(LayoutTypeNode::class)]
#[CoversClass(Configuration::class)]
final class LayoutTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testLayoutTypeSettings(): void
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'name' => 'layout',
                        'icon' => '/icon.svg',
                        'zones' => [
                            'zone' => [
                                'name' => 'zone',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'zones' => [
                        'zone' => [
                            'name' => 'zone',
                            'allowed_block_definitions' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types',
        );
    }

    public function testLayoutTypeSettingsWithNoIcon(): void
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'icon' => null,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.icon',
        );
    }

    public function testLayoutTypeSettingsWithNullIcon(): void
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'icon' => null,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'icon' => null,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.icon',
        );
    }

    public function testLayoutTypeSettingsNoZonesMerge(): void
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'zones' => [
                            'left' => [
                                'name' => 'Left',
                            ],
                            'right' => [
                                'name' => 'Right',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'layout_types' => [
                    'layout' => [
                        'zones' => [
                            'top' => [
                                'name' => 'Top',
                            ],
                            'bottom' => [
                                'name' => 'Bottom',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'zones' => [
                        'top' => [
                            'name' => 'Top',
                            'allowed_block_definitions' => [],
                        ],
                        'bottom' => [
                            'name' => 'Bottom',
                            'allowed_block_definitions' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.zones',
        );
    }

    public function testLayoutTypeSettingsWithAllowedBlockDefinitions(): void
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'zones' => [
                            'zone' => [
                                'allowed_block_definitions' => ['title', 'text'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'zones' => [
                        'zone' => [
                            'allowed_block_definitions' => ['title', 'text'],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.zones.*.allowed_block_definitions',
        );
    }

    public function testLayoutTypeSettingsWithNonUniqueAllowedBlockDefinitions(): void
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'zones' => [
                            'zone' => [
                                'allowed_block_definitions' => ['title', 'text', 'title'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'zones' => [
                        'zone' => [
                            'allowed_block_definitions' => ['title', 'text'],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.zones.*.allowed_block_definitions',
        );
    }

    public function testLayoutTypeSettingsWithEmptyLayouts(): void
    {
        $config = ['layout_types' => []];
        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testLayoutTypeSettingsWithNoName(): void
    {
        $config = ['layout_types' => ['layout' => []]];
        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testLayoutTypeSettingsWithNoZones(): void
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testLayoutTypeWithEmptyIcon(): void
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'Layout',
                    'icon' => '',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config], 'Icon path needs to be a non empty string or null.');
    }

    public function testLayoutTypeWithNonStringIcon(): void
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'Layout',
                    'icon' => 42,
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config], 'Icon path needs to be a non empty string or null.');
    }

    public function testLayoutTypeSettingsWithEmptyZones(): void
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                    'zones' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testLayoutTypeSettingsWithNoZoneName(): void
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                    'zones' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testLayoutTypeSettingsWithEmptyAllowedBlockDefinitions(): void
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                    'zones' => [
                        'name' => 'zone',
                        'allowed_block_definitions' => [],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
