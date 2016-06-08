<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class NetgenBlockManagerExtensionTwigTest extends \Twig_Test_IntegrationTestCase
{
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
        $this->globalHelperMock = $this->createMock(GlobalHelper::class);

        $this->viewRendererMock = $this->createMock(RendererInterface::class);

        $this->fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $this->viewRendererMock
            ->expects($this->any())
            ->method('renderValueObject')
            ->will(
                $this->returnCallback(
                    function ($block, $context) {
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
