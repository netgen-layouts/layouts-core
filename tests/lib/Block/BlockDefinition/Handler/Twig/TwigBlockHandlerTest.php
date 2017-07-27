<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler\Twig;

use Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\TwigBlockHandler;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\BlockTranslation;
use Netgen\BlockManager\Parameters\ParameterValue;
use PHPUnit\Framework\TestCase;

class TwigBlockHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\TwigBlockHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new TwigBlockHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\TwigBlockHandler::isContextual
     */
    public function testIsContextual()
    {
        $this->assertTrue($this->handler->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\TwigBlockHandler::getTwigBlockName
     */
    public function testGetTwigBlockName()
    {
        $block = new Block(
            array(
                'availableLocales' => array('en'),
                'translations' => array(
                    'en' => new BlockTranslation(
                        array(
                            'parameters' => array(
                                'block_name' => new ParameterValue(
                                    array(
                                        'value' => 'twig_block',
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $this->assertEquals('twig_block', $this->handler->getTwigBlockName($block));
    }
}
