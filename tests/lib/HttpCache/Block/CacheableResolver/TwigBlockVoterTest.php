<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\TwigBlockVoter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinition;
use PHPUnit\Framework\TestCase;

class TwigBlockVoterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolver\TwigBlockVoter
     */
    protected $voter;

    public function setUp()
    {
        $this->voter = new TwigBlockVoter();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\TwigBlockVoter::vote
     */
    public function testVote()
    {
        $block = new Block(
            array(
                'definition' => new TwigBlockDefinition('twig_block'),
            )
        );

        $this->assertFalse($this->voter->vote($block));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\TwigBlockVoter::vote
     */
    public function testVoteWithNonTwigBlock()
    {
        $block = new Block(
            array(
                'definition' => new BlockDefinition('block'),
            )
        );

        $this->assertNull($this->voter->vote($block));
    }
}
