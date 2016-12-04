<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler;

use Closure;
use Netgen\BlockManager\Block\BlockDefinition\Handler\FullViewHandler;
use Netgen\BlockManager\Block\BlockDefinition\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\Core\Values\Page\Block;
use PHPUnit\Framework\TestCase;

class FullViewHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\FullViewHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new FullViewHandler('content');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\FullViewHandler::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\FullViewHandler::getTwigBlockName
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        $twigTemplate = $this->createMock(ContextualizedTwigTemplate::class);

        $twigTemplate
            ->expects($this->once())
            ->method('renderBlock')
            ->with($this->equalTo('content'))
            ->will($this->returnValue('rendered'));

        $dynamicParameters = $this->handler->getDynamicParameters(
            new Block(),
            array('twigTemplate' => $twigTemplate)
        );

        $this->assertInternalType('array', $dynamicParameters);
        $this->assertArrayHasKey('content', $dynamicParameters);
        $this->assertInstanceOf(Closure::class, $dynamicParameters['content']);
        $this->assertEquals('rendered', $dynamicParameters['content']());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler::getDynamicParameters
     */
    public function testGetDynamicParametersWithoutTwigTemplate()
    {
        $dynamicParameters = $this->handler->getDynamicParameters(new Block());

        $this->assertInternalType('array', $dynamicParameters);
        $this->assertArrayHasKey('content', $dynamicParameters);
        $this->assertInstanceOf(Closure::class, $dynamicParameters['content']);
        $this->assertEquals('', $dynamicParameters['content']());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler::getDynamicParameters
     */
    public function testGetDynamicParametersWithInvalidTwigTemplate()
    {
        $dynamicParameters = $this->handler->getDynamicParameters(
            new Block(),
            array('twigTemplate' => 42)
        );

        $this->assertInternalType('array', $dynamicParameters);
        $this->assertArrayHasKey('content', $dynamicParameters);
        $this->assertInstanceOf(Closure::class, $dynamicParameters['content']);
        $this->assertEquals('', $dynamicParameters['content']());
    }
}
