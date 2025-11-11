<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\PageLayoutNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(PageLayoutNode::class)]
#[CoversClass(Configuration::class)]
final class PageLayoutNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

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
            'pagelayout',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
