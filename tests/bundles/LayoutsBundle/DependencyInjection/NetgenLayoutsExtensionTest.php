<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs\ExtensionPlugin;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::getConfiguration
 * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::load
 * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::loadConfigFiles
 */
final class NetgenLayoutsExtensionTest extends AbstractExtensionTestCase
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
     * @var \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension
     */
    private $extension;

    public function setUp(): void
    {
        parent::setUp();

        /** @var \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension $extension */
        $extension = $this->container->getExtension('netgen_layouts');

        $this->extension = $extension;
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::addPlugin
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::hasPlugin
     */
    public function testHasPlugin(): void
    {
        self::assertTrue($this->extension->hasPlugin(ExtensionPlugin::class));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::addPlugin
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::getPlugin
     */
    public function testGetPlugin(): void
    {
        self::assertInstanceOf(ExtensionPlugin::class, $this->extension->getPlugin(ExtensionPlugin::class));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::addPlugin
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::getPlugin
     */
    public function testGetPluginThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Extension plugin "unknown" does not exist');

        $this->extension->getPlugin('unknown');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::addPlugin
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::getPlugins
     */
    public function testGetPlugins(): void
    {
        $plugins = $this->extension->getPlugins();

        self::assertArrayHasKey(ExtensionPlugin::class, $plugins);
        self::assertCount(1, $plugins);
        self::assertInstanceOf(ExtensionPlugin::class, $plugins[ExtensionPlugin::class]);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::prepend
     */
    public function testAppendFromPlugin(): void
    {
        $this->extension->prepend($this->container);

        $config = array_merge_recursive(
            ...$this->container->getExtensionConfig('netgen_layouts')
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
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::load
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::validateCurrentDesign
     */
    public function testNonExistingCurrentDesign(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Design "non_existing" does not exist. Available designs are: standard');

        $this->load($this->minimalConfig + ['design' => 'non_existing']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::load
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::validateCurrentDesign
     */
    public function testStandardAsCurrentDesign(): void
    {
        $this->load($this->minimalConfig + ['design' => 'standard']);

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::load
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::validateCurrentDesign
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
        $extension = new NetgenLayoutsExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return [$extension];
    }
}
