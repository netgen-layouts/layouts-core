<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Extension;

use InvalidArgumentException;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\Locale\LocaleProviderInterface;
use Netgen\Layouts\Tests\Stubs\ErrorHandler;
use Netgen\Layouts\View\RendererInterface;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\Test\IntegrationTestCase;

use function count;
use function file_get_contents;
use function preg_match;
use function preg_match_all;
use function realpath;
use function sprintf;
use function str_contains;
use function str_replace;

use const PREG_SET_ORDER;

#[CoversClass(RenderingRuntime::class)]
final class RenderingExtensionTwigTest extends IntegrationTestCase
{
    private Stub&BlockService $blockServiceStub;

    private Stub&RendererInterface $rendererStub;

    private Stub&LocaleProviderInterface $localeProviderStub;

    private RequestStack $requestStack;

    private RenderingExtension $extension;

    private RenderingRuntime $runtime;

    protected function setUp(): void
    {
        $this->blockServiceStub = self::createStub(BlockService::class);
        $this->rendererStub = self::createStub(RendererInterface::class);
        $this->localeProviderStub = self::createStub(LocaleProviderInterface::class);
        $this->requestStack = new RequestStack();

        $this->extension = new RenderingExtension();
        $this->runtime = new RenderingRuntime(
            $this->blockServiceStub,
            $this->rendererStub,
            $this->localeProviderStub,
            $this->requestStack,
            new ErrorHandler(),
            new Environment(new ArrayLoader()),
        );
    }

    #[DataProvider('integrationDataProvider')]
    public function testIntegration(mixed $file, mixed $message, mixed $condition, mixed $templates, mixed $exception, mixed $outputs, mixed $deprecation = ''): void
    {
        $this->configureStubs();

        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    #[DataProvider('integrationDataProvider')]
    public function testIntegrationWithLocale(mixed $file, mixed $message, mixed $condition, mixed $templates, mixed $exception, mixed $outputs, mixed $deprecation = ''): void
    {
        $request = Request::create('');
        $this->requestStack->push($request);

        $this->configureStubs();

        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    #[DataProvider('legacyIntegrationDataProvider')]
    public function testLegacyIntegration(mixed $file, mixed $message, mixed $condition, mixed $templates, mixed $exception, mixed $outputs, mixed $deprecation = ''): void
    {
        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    /**
     * @return iterable<mixed>
     */
    public static function integrationDataProvider(): iterable
    {
        return self::assembleDataProvider(false);
    }

    /**
     * @return iterable<mixed>
     */
    public static function legacyIntegrationDataProvider(): iterable
    {
        return self::assembleDataProvider(true);
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

    protected static function getFixturesDirectory(): string
    {
        return __DIR__ . '/_fixtures/';
    }

    private function configureStubs(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request instanceof Request) {
            $this->localeProviderStub
                ->method('getRequestLocales')
                ->willReturn(['en']);
        }

        $this->blockServiceStub
            ->method('loadZoneBlocks')
            ->willReturn(BlockList::fromArray([]));

        $this->rendererStub
            ->method('renderValue')
            ->willReturnCallback(
                static fn (ZoneReference $zoneReference, string $context): string => $context === 'json' ?
                    '{"blocks":[{"id":1},{"id":2}]}' :
                    'block1 block2',
            );
    }

    /**
     * @return iterable<mixed>
     */
    private static function assembleDataProvider(bool $legacyTests): iterable
    {
        $fixturesDir = self::getFixturesDirectory();
        $fixturesDir = (string) realpath($fixturesDir);
        $tests = [];

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fixturesDir), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            /** @var \SplFileInfo $file */
            if (preg_match('/\.test$/', $file->getRealPath()) === 0) {
                continue;
            }

            if ($legacyTests xor str_contains($file->getRealPath(), '.legacy.test')) {
                continue;
            }

            $test = (string) file_get_contents($file->getRealPath());
            $testPath = str_replace($fixturesDir . '/', '', $file->getRealPath());

            if (preg_match('/--TEST--\s*(.*?)\s*(?:--CONDITION--\s*(.*))?\s*(?:--DEPRECATION--\s*(.*?))?\s*((?:--TEMPLATE(?:\(.*?\))?--(?:.*?))+)\s*(?:--DATA--\s*(.*))?\s*--EXCEPTION--\s*(.*)/sx', $test, $match) > 0) {
                $message = $match[1];
                $condition = $match[2];
                $deprecation = $match[3];
                $templates = self::parseTemplates($match[4]);
                $exception = $match[6];
                $outputs = [[null, $match[5], null, '']];
            } elseif (preg_match('/--TEST--\s*(.*?)\s*(?:--CONDITION--\s*(.*))?\s*(?:--DEPRECATION--\s*(.*?))?\s*((?:--TEMPLATE(?:\(.*?\))?--(?:.*?))+)--DATA--.*?--EXPECT--.*/s', $test, $match) > 0) {
                $message = $match[1];
                $condition = $match[2];
                $deprecation = $match[3];
                $templates = self::parseTemplates($match[4]);
                $exception = false;
                preg_match_all('/--DATA--(.*?)(?:--CONFIG--(.*?))?--EXPECT--(.*?)(?=--DATA--|$)/s', $test, $outputs, PREG_SET_ORDER);
            } else {
                throw new InvalidArgumentException(sprintf('Test "%s" is not valid.', $testPath));
            }

            $tests[$testPath] = [$testPath, $message, $condition, $templates, $exception, $outputs, $deprecation];
        }

        if ($legacyTests && count($tests) === 0) {
            // add a dummy test to avoid a PHPUnit message
            return [['not', '-', '', [], '', []]];
        }

        return $tests;
    }
}
