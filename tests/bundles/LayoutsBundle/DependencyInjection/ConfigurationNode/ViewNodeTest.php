<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\ViewNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(ViewNode::class)]
#[CoversClass(Configuration::class)]
final class ViewNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testViewSettings(): void
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'template' => 'block.html.twig',
                                'match' => [
                                    'block_identifier' => 42,
                                ],
                                'parameters' => [
                                    'param' => 'value',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [
                                'block_identifier' => 42,
                            ],
                            'parameters' => [
                                'param' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view',
        );
    }

    public function testViewSettingsWithMatchWithArrayValues(): void
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'match' => [24, 42],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'match' => [24, 42],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view.*.*.*.match',
        );
    }

    public function testViewSettingsWithEmptyMatch(): void
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'match' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'match' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view.*.*.*.match',
        );
    }

    public function testViewSettingsWithNoParameters(): void
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'parameters' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view.*.*.*.parameters',
        );
    }

    public function testViewSettingsWithNoMatch(): void
    {
        $config = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testViewSettingsWithNoTemplate(): void
    {
        $config = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'match' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testViewSettingsWithNoBlocks(): void
    {
        $config = [
            'view' => [
                'block_view' => [
                    'some_context' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testViewSettingsWithNoContext(): void
    {
        $config = [
            'view' => [
                'block_view' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testViewSettingsWithNoViews(): void
    {
        $config = [
            'view' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
