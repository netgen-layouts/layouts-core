<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler;

class NetgenBlockManagerExtensionTwigTest extends \Twig_Test_IntegrationTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewRendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fragmentHandlerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();

        $this->blockDefinitionRegistry->addBlockDefinition(
            new BlockDefinition(
                'block_definition',
                new BlockDefinitionHandler(),
                new Configuration('block_definition', array(), array())
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition(
            new BlockDefinition(
                'twig_block',
                new TwigBlockHandler(),
                new Configuration('twig_block', array(), array())
            )
        );

        $this->globalHelperMock = $this->createMock(GlobalHelper::class);

        $this->viewRendererMock = $this->createMock(RendererInterface::class);

        $this->fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $this->viewRendererMock
            ->expects($this->any())
            ->method('renderValueObject')
            ->will(
                $this->returnCallback(
                    function ($block, $parameters, $context) {
                        if ($context === ViewInterface::CONTEXT_VIEW) {
                            return 'rendered block';
                        } elseif ($context === 'json') {
                            return '{"block_id": 5}';
                        }

                        return '';
                    }
                )
            );

        $this->extension = new NetgenBlockManagerExtension(
            $this->blockDefinitionRegistry,
            $this->globalHelperMock,
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
