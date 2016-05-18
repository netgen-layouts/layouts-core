<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\Bundle\BlockManagerBundle\Renderer\BlockRendererInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;

class NetgenBlockManagerExtensionTwigTest extends \Twig_Test_IntegrationTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockRendererMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->globalHelperMock = $this->getMockBuilder(GlobalHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockRendererMock = $this->getMockBuilder(BlockRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockRendererMock
            ->expects($this->any())
            ->method('renderBlockFragment')
            ->will($this->returnValue('rendered block'));

        $this->extension = new NetgenBlockManagerExtension(
            $this->globalHelperMock,
            $this->blockRendererMock
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
