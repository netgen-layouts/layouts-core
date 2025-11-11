<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode;
use Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(AppNode::class)]
final class AppNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testJavascripts(): void
    {
        $config = [
            [
                'app' => [
                    'javascripts' => [
                        'script',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'app' => [
                'javascripts' => [
                    'script',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'app.javascripts',
        );
    }

    public function testJavascriptsWithNoJavascripts(): void
    {
        $config = [
            [
                'app' => [],
            ],
        ];

        $expectedConfig = [
            'app' => [
                'javascripts' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'app.javascripts',
        );
    }

    public function testJavascriptsWithEmptyJavascripts(): void
    {
        $config = [
            [
                'app' => [
                    'javascripts' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.app.javascripts" should have at least 1 element(s) defined.');
    }

    public function testJavascriptsWithInvalidJavascripts(): void
    {
        $config = [
            [
                'app' => [
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
                'app' => [
                    'javascripts' => [
                        42,
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'Invalid type for path "netgen_layouts.app.javascripts.0". Expected "string", but got "int"');
    }

    public function testStylesheets(): void
    {
        $config = [
            [
                'app' => [
                    'stylesheets' => [
                        'script',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'app' => [
                'stylesheets' => [
                    'script',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'app.stylesheets',
        );
    }

    public function testStylesheetsWithNoStylesheets(): void
    {
        $config = [
            [
                'app' => [],
            ],
        ];

        $expectedConfig = [
            'app' => [
                'stylesheets' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'app.stylesheets',
        );
    }

    public function testStylesheetsWithEmptyStylesheets(): void
    {
        $config = [
            [
                'app' => [
                    'stylesheets' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.app.stylesheets" should have at least 1 element(s) defined.');
    }

    public function testStylesheetsWithInvalidStylesheets(): void
    {
        $config = [
            [
                'app' => [
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
                'app' => [
                    'stylesheets' => [
                        42,
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'Invalid type for path "netgen_layouts.app.stylesheets.0". Expected "string", but got "int"');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        $extension = new NetgenLayoutsExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return new Configuration($extension);
    }
}
