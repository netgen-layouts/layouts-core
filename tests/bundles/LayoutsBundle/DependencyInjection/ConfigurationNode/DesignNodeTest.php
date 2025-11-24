<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(DesignNode::class)]
#[CoversClass(Configuration::class)]
final class DesignNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testDesignSettings(): void
    {
        $config = [
            [],
        ];

        $expectedConfig = [
            'design' => 'standard',
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design',
        );
    }

    public function testDesignSettingsWithEmptyDesignName(): void
    {
        $config = [
            [
                'design' => '',
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.design" cannot contain an empty value, but got "".');
    }

    public function testDesignSettingsWithInvalidDesignName(): void
    {
        $config = [
            [
                'design' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid(
            $config,
            'Invalid type for path "netgen_layouts.design". Expected "string", but got "array".',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
