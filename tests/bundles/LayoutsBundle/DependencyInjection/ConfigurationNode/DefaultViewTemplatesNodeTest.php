<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DefaultViewTemplatesNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(DefaultViewTemplatesNode::class)]
#[CoversClass(Configuration::class)]
final class DefaultViewTemplatesNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

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
            'default_view_templates',
        );
    }

    public function testDefaultViewTemplatesSettingsWithNoContexts(): void
    {
        $config = [
            'default_view_templates' => [
                'view' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

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
        return new Configuration(new NetgenLayoutsExtension());
    }
}
