<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AdminNode;
use Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(AdminNode::class)]
final class AdminNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testJavascripts(): void
    {
        $config = [
            [
                'admin' => [
                    'javascripts' => [
                        'script',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'admin' => [
                'javascripts' => [
                    'script',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'admin.javascripts',
        );
    }

    public function testJavascriptsWithNoJavascripts(): void
    {
        $config = [
            [
                'admin' => [],
            ],
        ];

        $expectedConfig = [
            'admin' => [
                'javascripts' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'admin.javascripts',
        );
    }

    public function testJavascriptsWithEmptyJavascripts(): void
    {
        $config = [
            [
                'admin' => [
                    'javascripts' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.admin.javascripts" should have at least 1 element(s) defined.');
    }

    public function testJavascriptsWithInvalidJavascripts(): void
    {
        $config = [
            [
                'admin' => [
                    'javascripts' => 'script',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config);
    }

    public function testJavascriptsWithInvalidJavascript(): void
    {
        $config = [
            [
                'admin' => [
                    'javascripts' => [
                        42,
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The value should be a string');
    }

    public function testStylesheets(): void
    {
        $config = [
            [
                'admin' => [
                    'stylesheets' => [
                        'script',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'admin' => [
                'stylesheets' => [
                    'script',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'admin.stylesheets',
        );
    }

    public function testStylesheetsWithNoStylesheets(): void
    {
        $config = [
            [
                'admin' => [],
            ],
        ];

        $expectedConfig = [
            'admin' => [
                'stylesheets' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'admin.stylesheets',
        );
    }

    public function testStylesheetsWithEmptyStylesheets(): void
    {
        $config = [
            [
                'admin' => [
                    'stylesheets' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.admin.stylesheets" should have at least 1 element(s) defined.');
    }

    public function testStylesheetsWithInvalidStylesheets(): void
    {
        $config = [
            [
                'admin' => [
                    'stylesheets' => 'script',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config);
    }

    public function testStylesheetsWithInvalidStylesheet(): void
    {
        $config = [
            [
                'admin' => [
                    'stylesheets' => [
                        42,
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The value should be a string');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        $extension = new NetgenLayoutsExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return new Configuration($extension);
    }
}
