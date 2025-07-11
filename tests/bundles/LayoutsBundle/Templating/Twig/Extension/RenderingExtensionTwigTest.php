<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Locale\LocaleProviderInterface;
use Netgen\Layouts\Tests\Stubs\ErrorHandler;
use Netgen\Layouts\View\RendererInterface;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\Test\IntegrationTestCase;

final class RenderingExtensionTwigTest extends IntegrationTestCase
{
    private MockObject $blockServiceMock;

    private MockObject $rendererMock;

    private MockObject $localeProviderMock;

    private RequestStack $requestStack;

    private RenderingExtension $extension;

    private RenderingRuntime $runtime;

    protected function setUp(): void
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
            new ErrorHandler(),
            new Environment(new ArrayLoader()),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderZone
     *
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
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderZone
     *
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

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderZone
     *
     * @dataProvider getLegacyTests
     *
     * @group legacy
     *
     * @param mixed $file
     * @param mixed $message
     * @param mixed $condition
     * @param mixed $templates
     * @param mixed $exception
     * @param mixed $outputs
     * @param mixed $deprecation
     */
    public function testLegacyIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation = ''): void
    {
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
                    RenderingRuntime::class => fn (): RenderingRuntime => $this->runtime,
                ],
            ),
        ];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__ . '/_fixtures/';
    }

    protected static function getFixturesDirectory(): string
    {
        return __DIR__ . '/_fixtures/';
    }

    private function configureMocks(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $request instanceof Request ?
            $this->localeProviderMock
                ->method('getRequestLocales')
                ->with(self::identicalTo($request))
                ->willReturn(['en']) :
            $this->localeProviderMock
                ->expects(self::never())
                ->method('getRequestLocales');

        $this->blockServiceMock
            ->method('loadZoneBlocks')
            ->with(
                self::isInstanceOf(Zone::class),
                self::identicalTo($request instanceof Request ? ['en'] : null),
            )
            ->willReturn(new BlockList());

        $this->rendererMock
            ->method('renderValue')
            ->willReturnCallback(
                static fn (ZoneReference $zoneReference, string $context): string => $context === 'json' ?
                        '{"blocks":[{"id":1},{"id":2}]}' :
                        'block1 block2',
            );
    }
}
