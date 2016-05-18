<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\TwigBlock;
use Netgen\BlockManager\Parameters\Parameter;

class TwigBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\TwigBlock
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new TwigBlock();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\TwigBlock::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('twig_block', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\TwigBlock::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'block_name' => new Parameter\Identifier(array(), true),
            ),
            $this->blockDefinition->getParameters()
        );
    }
}
