<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Stubs\ExtensionPlugin;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::getConfiguration
 * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
 * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::loadConfigFiles
 */
final class NetgenBlockManagerExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @var array
     */
    private $minimalConfig = [
        'design_list' => [
            'standard' => [],
        ],
    ];

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension
     */
    private $extension;

    public function setUp(): void
    {
        parent::setUp();

        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $extension */
        $extension = $this->container->getExtension('netgen_block_manager');

        $this->extension = $extension;
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::addPlugin
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::hasPlugin
     */
    public function testHasPlugin(): void
    {
        self::assertTrue($this->extension->hasPlugin(ExtensionPlugin::class));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::addPlugin
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::getPlugin
     */
    public function testGetPlugin(): void
    {
        self::assertInstanceOf(ExtensionPlugin::class, $this->extension->getPlugin(ExtensionPlugin::class));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::addPlugin
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::getPlugin
     */
    public function testGetPluginThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Extension plugin "unknown" does not exist');

        $this->extension->getPlugin('unknown');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::addPlugin
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::getPlugins
     */
    public function testGetPlugins(): void
    {
        $plugins = $this->extension->getPlugins();

        self::assertArrayHasKey(ExtensionPlugin::class, $plugins);
        self::assertCount(1, $plugins);
        self::assertInstanceOf(ExtensionPlugin::class, $plugins[ExtensionPlugin::class]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::prepend
     */
    public function testAppendFromPlugin(): void
    {
        $this->extension->prepend($this->container);

        $config = array_merge_recursive(
            ...$this->container->getExtensionConfig('netgen_block_manager')
        );

        self::assertArrayHasKey('block_types', $config);
        self::assertArrayHasKey('test_type', $config['block_types']);

        self::assertSame(
            [
                'name' => 'Test type',
                'definition_identifier' => 'title',
            ],
            $config['block_types']['test_type']
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::validateCurrentDesign
     */
    public function testNonExistingCurrentDesign(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Design "non_existing" does not exist. Available designs are: standard');

        $this->load($this->minimalConfig + ['design' => 'non_existing']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::validateCurrentDesign
     */
    public function testStandardAsCurrentDesign(): void
    {
        $this->load($this->minimalConfig + ['design' => 'standard']);

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::validateCurrentDesign
     */
    public function testCustomDesignAsCurrentDesign(): void
    {
        $designList = $this->minimalConfig;
        $designList['design_list']['custom'] = [];

        $this->load($designList + ['design' => 'custom']);

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    protected function getContainerExtensions(): array
    {
        $extension = new NetgenBlockManagerExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return [$extension];
    }
}
