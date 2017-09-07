<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Extension;

use Exception;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\BlockTranslation;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Environment;
use Twig_Error;
use Twig_FactoryRuntimeLoader;
use Twig_Loader_Array;

class RenderingExtensionTwigTest extends \Twig_Test_IntegrationTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeProviderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension
     */
    protected $extension;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime
     */
    protected $runtime;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);
        $this->rendererMock = $this->createMock(RendererInterface::class);
        $this->localeProviderMock = $this->createMock(LocaleProviderInterface::class);

        $this->blockServiceMock
            ->expects($this->any())
            ->method('loadZoneBlocks')
            ->will(
                $this->returnValue(
                    array(
                        new Block(
                            array(
                                'definition' => new BlockDefinition(
                                    'block_definition'
                                ),
                            )
                        ),
                        new Block(
                            array(
                                'definition' => new BlockDefinition(
                                    'twig_block'
                                ),
                                'availableLocales' => array('en'),
                                'translations' => array(
                                    'en' => new BlockTranslation(
                                        array(
                                            'parameters' => array(
                                                'block_name' => new ParameterValue(
                                                    array(
                                                        'name' => 'block_name',
                                                        'value' => 'my_block',
                                                    )
                                                ),
                                            ),
                                        )
                                    ),
                                ),
                            )
                        ),
                        new Block(
                            array(
                                'definition' => new BlockDefinition(
                                    'block_definition'
                                ),
                            )
                        ),
                    )
                )
            );

        $this->rendererMock
            ->expects($this->any())
            ->method('renderValueObject')
            ->will(
                $this->returnCallback(
                    function (Block $block, $context) {
                        if ($block->getDefinition()->getIdentifier() === 'twig_block') {
                            return 'rendered twig block' . PHP_EOL;
                        } elseif ($context === ViewInterface::CONTEXT_DEFAULT) {
                            return 'rendered block' . PHP_EOL;
                        } elseif ($context === 'json') {
                            return '{"block_id": 5}' . PHP_EOL;
                        }

                        return '';
                    }
                )
            );

        $this->extension = new RenderingExtension();
        $this->runtime = new RenderingRuntime(
            $this->blockServiceMock,
            $this->rendererMock,
            $this->localeProviderMock,
            new RequestStack()
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
     */
    public function testIntegration($file, $message, $condition, $templates, $exception, $outputs)
    {
        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs);
    }

    /**
     * @return \Twig_ExtensionInterface[]
     */
    protected function getExtensions()
    {
        return array($this->extension);
    }

    protected function getRuntimeLoaders()
    {
        return array(
            new Twig_FactoryRuntimeLoader(
                array(
                    RenderingRuntime::class => function () {
                        return $this->runtime;
                    },
                )
            ),
        );
    }

    /**
     * @return string
     */
    protected function getFixturesDir()
    {
        return __DIR__ . '/_fixtures/';
    }

    protected function doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs)
    {
        if (!$outputs) {
            $this->markTestSkipped('no legacy tests to run');
        }

        if ($condition) {
            eval('$ret = ' . $condition . ';');
            if (!$ret) {
                $this->markTestSkipped($condition);
            }
        }

        $loader = new Twig_Loader_Array($templates);

        foreach ($outputs as $i => $match) {
            $config = array_merge(array(
                'cache' => false,
                'strict_variables' => true,
            ), $match[2] ? eval($match[2] . ';') : array());
            $twig = new Twig_Environment($loader, $config);
            $twig->addGlobal('global', 'global');
            foreach ($this->getExtensions() as $extension) {
                $twig->addExtension($extension);
            }

            foreach ($this->getTwigFilters() as $filter) {
                $twig->addFilter($filter);
            }

            foreach ($this->getTwigTests() as $test) {
                $twig->addTest($test);
            }

            foreach ($this->getTwigFunctions() as $function) {
                $twig->addFunction($function);
            }

            foreach ($this->getRuntimeLoaders() as $runtimeLoader) {
                $twig->addRuntimeLoader($runtimeLoader);
            }

            // avoid using the same PHP class name for different cases
            $p = new ReflectionProperty($twig, 'templateClassPrefix');
            $p->setAccessible(true);
            $p->setValue($twig, '__TwigTemplate_' . hash('sha256', uniqid(mt_rand(), true), false) . '_');

            try {
                $template = $twig->loadTemplate('index.twig');
            } catch (Exception $e) {
                if (false !== $exception) {
                    $message = $e->getMessage();
                    $this->assertSame(trim($exception), trim(sprintf('%s: %s', get_class($e), $message)));
                    $last = substr($message, strlen($message) - 1);
                    $this->assertTrue('.' === $last || '?' === $last, $message, 'Exception message must end with a dot or a question mark.');

                    return;
                }

                throw new Twig_Error(sprintf('%s: %s', get_class($e), $e->getMessage()), -1, $file, $e);
            }

            try {
                $output = trim($template->render(eval($match[1] . ';')), "\n ");
            } catch (Exception $e) {
                if (false !== $exception) {
                    $this->assertSame(trim($exception), trim(sprintf('%s: %s', get_class($e), $e->getMessage())));

                    return;
                }

                $e = new Twig_Error(sprintf('%s: %s', get_class($e), $e->getMessage()), -1, $file, $e);

                $output = trim(sprintf('%s: %s', get_class($e), $e->getMessage()));
            }

            if (false !== $exception) {
                list($class) = explode(':', $exception);
                $this->assertThat(null, new PHPUnit_Framework_Constraint_Exception($class));
            }

            $expected = trim($match[3], "\n ");

            if ($expected !== $output) {
                printf("Compiled templates that failed on case %d:\n", $i + 1);

                foreach (array_keys($templates) as $name) {
                    echo "Template: $name\n";
                    echo $twig->compile($twig->parse($twig->tokenize($twig->getLoader()->getSourceContext($name))));
                }
            }
            $this->assertEquals($expected, $output, $message . ' (in ' . $file . ')');
        }
    }
}
