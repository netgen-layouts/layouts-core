<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignListNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(DesignListNode::class)]
#[CoversClass(Configuration::class)]
final class DesignListNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testDesignListSettings(): void
    {
        $config = [
            [
                'design_list' => [
                    'design1' => [
                        'theme1',
                        'theme2',
                    ],
                    'design2' => [
                        'theme2',
                        'theme3',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'design_list' => [
                'design1' => [
                    'theme1',
                    'theme2',
                ],
                'design2' => [
                    'theme2',
                    'theme3',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design_list',
        );
    }

    public function testDesignListSettingsWithEmptyDesignList(): void
    {
        $config = [
            'design_list' => [],
        ];

        $expectedConfig = [
            'design_list' => [],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design_list',
        );
    }

    public function testDesignListSettingsWithEmptyDesign(): void
    {
        $config = [
            [
                'design_list' => [
                    'design' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'design_list' => [
                'design' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design_list',
        );
    }

    public function testDesignListSettingsWithEmptyThemeName(): void
    {
        $config = [
            [
                'design_list' => [
                    'design' => [
                        '',
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.design_list.design.0" cannot contain an empty value, but got "".');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
