<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinition;
use PHPUnit\Framework\TestCase;

class ContainerVoterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolver\TwigBlockVoter
     */
    protected $voter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheableResolverMock;

    public function setUp()
    {
        $this->cacheableResolverMock = $this->createMock(CacheableResolverInterface::class);

        $this->voter = new ContainerVoter($this->cacheableResolverMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::vote
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

        $this->cacheableResolverMock
            ->expects($this->at(0))
            ->method('isCacheable')
            ->with($this->equalTo($containerBlock))
            ->will($this->returnValue(false));

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
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::vote
     */
    public function testVoteWithContainerWithoutTwigBlock()
    {
        $regularBlock = new Block(
            array(
                'definition' => new BlockDefinition('block'),
            )
        );

        $this->cacheableResolverMock
            ->expects($this->at(0))
            ->method('isCacheable')
            ->with($this->equalTo($regularBlock))
            ->will($this->returnValue(true));

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
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::vote
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
