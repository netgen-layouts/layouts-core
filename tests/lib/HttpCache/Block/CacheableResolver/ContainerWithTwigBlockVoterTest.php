<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerWithTwigBlockVoter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinition;
use PHPUnit\Framework\TestCase;

class ContainerWithTwigBlockVoterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolver\TwigBlockVoter
     */
    protected $voter;

    public function setUp()
    {
        $this->voter = new ContainerWithTwigBlockVoter();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerWithTwigBlockVoter::vote
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerWithTwigBlockVoter::containerHasTwigBlock
     */
    public function testVote()
    {
        $twigBlock = new Block(
            array(
                'definition' => new TwigBlockDefinition('twig_block'),
            )
        );

        $containerBlock = new Block(
            array(
                'definition' => new ContainerDefinition('container'),
                'placeholders' => array(
                    'left' => new Placeholder(
                        array(
                            'blocks' => array($twigBlock),
                        )
                    ),
                ),
            )
        );

        $block = new Block(
            array(
                'definition' => new ContainerDefinition('container'),
                'placeholders' => array(
                    'left' => new Placeholder(
                        array(
                            'blocks' => array($containerBlock),
                        )
                    ),
                ),
            )
        );

        $this->assertFalse($this->voter->vote($block));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerWithTwigBlockVoter::vote
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerWithTwigBlockVoter::containerHasTwigBlock
     */
    public function testVoteWithContainerWithoutTwigBlock()
    {
        $regularBlock = new Block(
            array(
                'definition' => new BlockDefinition('block'),
            )
        );

        $block = new Block(
            array(
                'definition' => new ContainerDefinition('container'),
                'placeholders' => array(
                    'left' => new Placeholder(
                        array(
                            'blocks' => array($regularBlock),
                        )
                    ),
                ),
            )
        );

        $this->assertNull($this->voter->vote($block));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerWithTwigBlockVoter::vote
     */
    public function testVoteWithNonContainerBlock()
    {
        $block = new Block(
            array(
                'definition' => new BlockDefinition('block'),
            )
        );

        $this->assertNull($this->voter->vote($block));
    }
}
