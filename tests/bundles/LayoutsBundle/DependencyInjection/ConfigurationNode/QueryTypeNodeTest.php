<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\QueryTypeNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(QueryTypeNode::class)]
#[CoversClass(Configuration::class)]
final class QueryTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testQueryTypeSettings(): void
    {
        $config = [
            [
                'query_types' => [
                    'type' => [
                        'name' => 'Type',
                        'handler' => 'handler',
                        'priority' => 100,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'query_types' => [
                'type' => [
                    'enabled' => true,
                    'name' => 'Type',
                    'handler' => 'handler',
                    'priority' => 100,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types',
        );
    }

    public function testQueryTypeSettingsWithDefaultPriority(): void
    {
        $config = [
            [
                'query_types' => [
                    'type' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'query_types' => [
                'type' => [
                    'priority' => 0,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types.*.priority',
        );
    }

    public function testQueryTypeSettingsWithNoHandler(): void
    {
        $config = [
            [
                'query_types' => [
                    'type' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'query_types' => [
                'type' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types.*.handler',
        );
    }

    public function testQueryTypeSettingsWithNoQueryTypes(): void
    {
        $config = [
            'query_types' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testQueryTypeSettingsWithNoName(): void
    {
        $config = [
            'query_types' => [
                'type' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testQueryTypeSettingsWithEmptyName(): void
    {
        $config = [
            'query_types' => [
                'type' => [
                    'name' => '',
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
