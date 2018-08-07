<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class DefaultViewTemplatesNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DefaultViewTemplatesNode::getConfigurationNode
     */
    public function testDefaultViewTemplatesSettings(): void
    {
        $config = [
            [
                'default_view_templates' => [
                    'view' => [
                        'context' => 'template.html.twig',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'default_view_templates' => [
                'view' => [
                    'context' => 'template.html.twig',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'default_view_templates'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DefaultViewTemplatesNode::getConfigurationNode
     */
    public function testDefaultViewTemplatesSettingsWithNoContexts(): void
    {
        $config = [
            'default_view_templates' => [
                'view' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DefaultViewTemplatesNode::getConfigurationNode
     */
    public function testDefaultViewTemplatesSettingsWithEmptyTemplate(): void
    {
        $config = [
            'default_view_templates' => [
                'view' => [
                    'context' => '',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DefaultViewTemplatesNode::getConfigurationNode
     */
    public function testDefaultViewTemplatesSettingsWithInvalidTemplate(): void
    {
        $config = [
            'default_view_templates' => [
                'view' => [
                    'context' => ['template.html.twig'],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
