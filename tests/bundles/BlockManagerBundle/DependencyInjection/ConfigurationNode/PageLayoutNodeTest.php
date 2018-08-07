<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class PageLayoutNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\PageLayoutNode::getConfigurationNode
     */
    public function testPagelayoutSettings(): void
    {
        $config = [
            [
                'pagelayout' => 'pagelayout.html.twig',
            ],
        ];

        $expectedConfig = [
            'pagelayout' => 'pagelayout.html.twig',
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'pagelayout'
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
