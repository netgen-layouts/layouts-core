<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler;

use Closure;
use Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler;
use Netgen\BlockManager\Block\BlockDefinition\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterValue;
use PHPUnit\Framework\TestCase;

class TwigBlockHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new TwigBlockHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler::getTwigBlockName
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        $twigTemplate = $this->createMock(ContextualizedTwigTemplate::class);

        $twigTemplate
            ->expects($this->once())
            ->method('renderBlock')
            ->with($this->equalTo('block'))
            ->will($this->returnValue('rendered'));

        $block = new Block(
            array(
                'parameters' => array(
                    'block_name' => new ParameterValue(
                        array(
                            'value' => 'block',
                        )
                    ),
                ),
            )
        );

        $dynamicParameters = $this->handler->getDynamicParameters(
            $block,
            array('twig_template' => $twigTemplate)
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
        $block = new Block(
            array(
                'parameters' => array(
                    'block_name' => new ParameterValue(
                        array(
                            'value' => 'block',
                        )
                    ),
                ),
            )
        );

        $dynamicParameters = $this->handler->getDynamicParameters($block);

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
        $block = new Block(
            array(
                'parameters' => array(
                    'block_name' => new ParameterValue(
                        array(
                            'value' => 'block',
                        )
                    ),
                ),
            )
        );

        $dynamicParameters = $this->handler->getDynamicParameters(
            $block,
            array('twig_template' => 42)
        );

        $this->assertInternalType('array', $dynamicParameters);
        $this->assertArrayHasKey('content', $dynamicParameters);
        $this->assertInstanceOf(Closure::class, $dynamicParameters['content']);
        $this->assertEquals('', $dynamicParameters['content']());
    }
}
