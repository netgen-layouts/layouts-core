<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualBlockVoter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use PHPUnit\Framework\TestCase;

class ContextualBlockVoterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualBlockVoter
     */
    protected $voter;

    public function setUp()
    {
        $this->voter = new ContextualBlockVoter();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualBlockVoter::vote
     */
    public function testVote()
    {
        $block = new Block(
            array(
                'definition' => new BlockDefinition('block'),
            )
        );

        $this->assertNull($this->voter->vote($block));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualBlockVoter::vote
     */
    public function testVoteWithContextualBlock()
    {
        $block = new Block(
            array(
                'definition' => new BlockDefinition(
                    'block',
                    array(),
                    new BlockDefinitionHandler(array(), true)
                ),
            )
        );

        $this->assertFalse($this->voter->vote($block));
    }
}
