<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class RenderingExtensionTwigTest extends \Twig_Test_IntegrationTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalVariableMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewRendererMock;

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
        $this->layoutServiceMock = $this->createMock(LayoutService::class);
        $this->globalVariableMock = $this->createMock(GlobalVariable::class);
        $this->viewRendererMock = $this->createMock(RendererInterface::class);
        $this->fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $this->viewRendererMock
            ->expects($this->any())
            ->method('renderValueObject')
            ->will(
                $this->returnCallback(
                    function ($block, $parameters, $context) {
                        if ($block->getBlockDefinition() instanceof TwigBlockDefinitionInterface) {
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

        $this->extension = new RenderingExtension(
            $this->layoutServiceMock,
            $this->globalVariableMock,
            $this->viewRendererMock,
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
