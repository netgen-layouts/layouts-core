<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler;

class RenderingExtensionTwigTest extends \Twig_Test_IntegrationTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry
     */
    protected $blockDefinitionRegistry;

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
        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();

        $this->blockDefinitionRegistry->addBlockDefinition(
            new BlockDefinition(
                'block_definition',
                new BlockDefinitionHandler(),
                new Configuration('block_definition')
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition(
            new BlockDefinition(
                'twig_block',
                new TwigBlockHandler(),
                new Configuration('twig_block')
            )
        );

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
                        if ($context === ViewInterface::CONTEXT_DEFAULT) {
                            return 'rendered block';
                        } elseif ($context === 'json') {
                            return '{"block_id": 5}';
                        }

                        return '';
                    }
                )
            );

        $this->extension = new RenderingExtension(
            $this->blockDefinitionRegistry,
            $this->layoutServiceMock,
            $this->globalVariableMock,
            $this->viewRendererMock,
            $this->fragmentHandlerMock
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
