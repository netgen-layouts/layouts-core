<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\CacheableResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Block\TwigBlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use PHPUnit\Framework\TestCase;

final class ContainerVoterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter
     */
    private $voter;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheableResolverMock;

    public function setUp()
    {
        $this->cacheableResolverMock = $this->createMock(CacheableResolverInterface::class);

        $this->voter = new ContainerVoter($this->cacheableResolverMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::__construct
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::vote
     */
    public function testVote()
    {
        $twigBlock = new Block(
            array(
                'definition' => new TwigBlockDefinition(),
            )
        );

        $containerBlock = new Block(
            array(
                'definition' => new ContainerDefinition(),
                'placeholders' => array(
                    'left' => new Placeholder(
                        array(
                            'blocks' => new ArrayCollection(array($twigBlock)),
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
                'definition' => new ContainerDefinition(),
                'placeholders' => array(
                    'left' => new Placeholder(
                        array(
                            'blocks' => new ArrayCollection(array($containerBlock)),
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
                'definition' => new BlockDefinition(),
            )
        );

        $this->cacheableResolverMock
            ->expects($this->at(0))
            ->method('isCacheable')
            ->with($this->equalTo($regularBlock))
            ->will($this->returnValue(true));

        $block = new Block(
            array(
                'definition' => new ContainerDefinition(),
                'placeholders' => array(
                    'left' => new Placeholder(
                        array(
                            'blocks' => new ArrayCollection(array($regularBlock)),
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
                'definition' => new BlockDefinition(),
            )
        );

        $this->assertNull($this->voter->vote($block));
    }
}
