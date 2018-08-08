<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\BlockList;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Stubs\ErrorHandler;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\Test\IntegrationTestCase;

final class RenderingExtensionTwigTest extends IntegrationTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $blockServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $rendererMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $localeProviderMock;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension
     */
    private $extension;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime
     */
    private $runtime;

    public function setUp(): void
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);
        $this->rendererMock = $this->createMock(RendererInterface::class);
        $this->localeProviderMock = $this->createMock(LocaleProviderInterface::class);
        $this->requestStack = new RequestStack();

        $this->extension = new RenderingExtension();
        $this->runtime = new RenderingRuntime(
            $this->blockServiceMock,
            $this->rendererMock,
            $this->localeProviderMock,
            $this->requestStack,
            new ErrorHandler()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::displayZone
     * @dataProvider getTests
     *
     * @param mixed $file
     * @param mixed $message
     * @param mixed $condition
     * @param mixed $templates
     * @param mixed $exception
     * @param mixed $outputs
     * @param mixed $deprecation
     */
    public function testIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation = ''): void
    {
        $this->configureMocks();

        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::displayZone
     * @dataProvider getTests
     *
     * @param mixed $file
     * @param mixed $message
     * @param mixed $condition
     * @param mixed $templates
     * @param mixed $exception
     * @param mixed $outputs
     * @param mixed $deprecation
     */
    public function testIntegrationWithLocale($file, $message, $condition, $templates, $exception, $outputs, $deprecation = ''): void
    {
        $request = Request::create('');
        $this->requestStack->push($request);

        $this->configureMocks();

        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    protected function getExtensions(): array
    {
        return [$this->extension];
    }

    protected function getRuntimeLoaders(): array
    {
        return [
            new FactoryRuntimeLoader(
                [
                    RenderingRuntime::class => function (): RenderingRuntime {
                        return $this->runtime;
                    },
                ]
            ),
        ];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__ . '/_fixtures/';
    }

    private function configureMocks(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $request instanceof Request ?
            $this->localeProviderMock
                ->expects(self::any())
                ->method('getRequestLocales')
                ->with(self::identicalTo($request))
                ->will(self::returnValue(['en'])) :
            $this->localeProviderMock
                ->expects(self::never())
                ->method('getRequestLocales');

        $this->blockServiceMock
            ->expects(self::any())
            ->method('loadZoneBlocks')
            ->with(
                self::isInstanceOf(Zone::class),
                self::identicalTo($request instanceof Request ? ['en'] : null)
            )
            ->will(
                self::returnValue(
                    new BlockList(
                        [
                            Block::fromArray(
                                [
                                    'definition' => BlockDefinition::fromArray(
                                        [
                                            'identifier' => 'block_definition',
                                        ]
                                    ),
                                ]
                            ),
                            Block::fromArray(
                                [
                                    'definition' => BlockDefinition::fromArray(
                                        [
                                            'identifier' => 'twig_block',
                                        ]
                                    ),
                                    'availableLocales' => ['en'],
                                    'locale' => 'en',
                                    'parameters' => [
                                        'block_name' => Parameter::fromArray(
                                            [
                                                'name' => 'block_name',
                                                'value' => 'my_block',
                                            ]
                                        ),
                                    ],
                                ]
                            ),
                            Block::fromArray(
                                [
                                    'definition' => BlockDefinition::fromArray(
                                        [
                                            'identifier' => 'block_definition',
                                        ]
                                    ),
                                ]
                            ),
                        ]
                    )
                )
            );

        $this->rendererMock
            ->expects(self::any())
            ->method('renderValue')
            ->will(
                self::returnCallback(
                    function (Block $block, string $context): string {
                        if ($block->getDefinition()->getIdentifier() === 'twig_block') {
                            return 'rendered twig block' . PHP_EOL;
                        }

                        if ($context === ViewInterface::CONTEXT_DEFAULT) {
                            return 'rendered block' . PHP_EOL;
                        }

                        if ($context === 'json') {
                            return '{"block_id": 5}' . PHP_EOL;
                        }

                        return '';
                    }
                )
            );
    }
}
