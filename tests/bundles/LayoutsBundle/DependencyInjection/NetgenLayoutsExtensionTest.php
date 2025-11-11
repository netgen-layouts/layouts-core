<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs\DummyExtensionPlugin;
use Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs\ExtensionPlugin;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use function array_merge_recursive;
use function sprintf;

#[CoversClass(NetgenLayoutsExtension::class)]
final class NetgenLayoutsExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @var mixed[]
     */
    private array $minimalConfig = [
        'design_list' => [
            'standard' => [],
        ],
    ];

    private NetgenLayoutsExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension $extension */
        $extension = $this->container->getExtension('netgen_layouts');

        $this->extension = $extension;
    }

    public function testHasPlugin(): void
    {
        self::assertTrue($this->extension->hasPlugin(ExtensionPlugin::class));
    }

    public function testGetPlugin(): void
    {
        self::assertInstanceOf(ExtensionPlugin::class, $this->extension->getPlugin(ExtensionPlugin::class));
    }

    public function testGetPluginThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Extension plugin "%s" does not exist', DummyExtensionPlugin::class));

        $this->extension->getPlugin(DummyExtensionPlugin::class);
    }

    public function testGetPlugins(): void
    {
        $plugins = $this->extension->getPlugins();

        self::assertArrayHasKey(ExtensionPlugin::class, $plugins);
        self::assertCount(1, $plugins);
        self::assertInstanceOf(ExtensionPlugin::class, $plugins[ExtensionPlugin::class]);
    }

    public function testAppendFromPlugin(): void
    {
        $this->extension->prepend($this->container);

        $config = array_merge_recursive(
            ...$this->container->getExtensionConfig('netgen_layouts'),
        );

        self::assertArrayHasKey('block_types', $config);
        self::assertArrayHasKey('test_type', $config['block_types']);

        self::assertSame(
            [
                'name' => 'Test type',
                'definition_identifier' => 'title',
            ],
            $config['block_types']['test_type'],
        );
    }

    public function testNonExistingCurrentDesign(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Design "non_existing" does not exist. Available designs are: standard');

        $this->load([...['design' => 'non_existing'], ...$this->minimalConfig]);
    }

    public function testStandardAsCurrentDesign(): void
    {
        $this->load([...['design' => 'standard'], ...$this->minimalConfig]);

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    public function testCustomDesignAsCurrentDesign(): void
    {
        $designList = $this->minimalConfig;
        $designList['design_list']['custom'] = [];

        $this->load([...['design' => 'custom'], ...$designList]);

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
