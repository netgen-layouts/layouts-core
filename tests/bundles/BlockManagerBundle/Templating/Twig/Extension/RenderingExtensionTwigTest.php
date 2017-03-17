<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class RenderingExtensionTwigTest extends \Twig_Test_IntegrationTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalVariableMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewRendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheableResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fragmentHandlerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);
        $this->globalVariableMock = $this->createMock(GlobalVariable::class);
        $this->viewBuilderMock = $this->createMock(ViewBuilderInterface::class);
        $this->viewRendererMock = $this->createMock(RendererInterface::class);
        $this->fragmentHandlerMock = $this->createMock(FragmentHandler::class);
        $this->cacheableResolverMock = $this->createConfiguredMock(
            CacheableResolverInterface::class,
            array(
                'isCacheable' => false,
            )
        );

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

        $this->viewBuilderMock
            ->expects($this->any())
            ->method('buildView')
            ->will(
                $this->returnCallback(
                    function ($block, $context, $parameters) {
                        $blockView = new BlockView(array('block' => $block) + $parameters);
                        $blockView->setContext($context);

                        return $blockView;
                    }
                )
            );

        $this->viewRendererMock
            ->expects($this->any())
            ->method('renderView')
            ->will(
                $this->returnCallback(
                    function (BlockViewInterface $blockView) {
                        if ($blockView->getBlock()->getDefinition()->getIdentifier() === 'twig_block') {
                            return 'rendered twig block' . PHP_EOL;
                        } elseif ($blockView->getContext() === ViewInterface::CONTEXT_DEFAULT) {
                            return 'rendered block' . PHP_EOL;
                        } elseif ($blockView->getContext() === 'json') {
                            return '{"block_id": 5}' . PHP_EOL;
                        }

                        return '';
                    }
                )
            );

        $this->extension = new RenderingExtension(
            $this->blockServiceMock,
            $this->globalVariableMock,
            $this->viewBuilderMock,
            $this->viewRendererMock,
            $this->cacheableResolverMock,
            $this->fragmentHandlerMock,
            'ngbm_block:viewBlockById'
        );
    }

    /**
     * @return \Twig_ExtensionInterface[]
     */
    protected function getExtensions()
    {
        return array($this->extension);
    }

    /**
     * @return string
     */
    protected function getFixturesDir()
    {
        return __DIR__ . '/_fixtures/';
    }
}
