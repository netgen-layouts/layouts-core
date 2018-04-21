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

        $this->voter = new ContainerVoter();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::vote
     */
    public function testVote()
    {
        $twigBlock = new Block(
            [
                'definition' => new TwigBlockDefinition(
                    [
                        'cacheableResolver' => $this->cacheableResolverMock,
                    ]
                ),
            ]
        );

        $containerBlock = new Block(
            [
                'definition' => new ContainerDefinition(
                    [
                        'cacheableResolver' => $this->cacheableResolverMock,
                    ]
                ),
                'placeholders' => [
                    'left' => new Placeholder(
                        [
                            'blocks' => new ArrayCollection([$twigBlock]),
                        ]
                    ),
                ],
            ]
        );

        $this->cacheableResolverMock
            ->expects($this->at(0))
            ->method('isCacheable')
            ->with($this->equalTo($containerBlock))
            ->will($this->returnValue(false));

        $block = new Block(
            [
                'definition' => new ContainerDefinition(
                    [
                        'cacheableResolver' => $this->cacheableResolverMock,
                    ]
                ),
                'placeholders' => [
                    'left' => new Placeholder(
                        [
                            'blocks' => new ArrayCollection([$containerBlock]),
                        ]
                    ),
                ],
            ]
        );

        $this->assertFalse($this->voter->vote($block));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::vote
     */
    public function testVoteWithContainerWithoutTwigBlock()
    {
        $regularBlock = new Block(
            [
                'definition' => new BlockDefinition(
                    [
                        'cacheableResolver' => $this->cacheableResolverMock,
                    ]
                ),
            ]
        );

        $this->cacheableResolverMock
            ->expects($this->at(0))
            ->method('isCacheable')
            ->with($this->equalTo($regularBlock))
            ->will($this->returnValue(true));

        $block = new Block(
            [
                'definition' => new ContainerDefinition(
                    [
                        'cacheableResolver' => $this->cacheableResolverMock,
                    ]
                ),
                'placeholders' => [
                    'left' => new Placeholder(
                        [
                            'blocks' => new ArrayCollection([$regularBlock]),
                        ]
                    ),
                ],
            ]
        );

        $this->assertNull($this->voter->vote($block));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::vote
     */
    public function testVoteWithNonContainerBlock()
    {
        $block = new Block(
            [
                'definition' => new BlockDefinition(
                    [
                        'cacheableResolver' => $this->cacheableResolverMock,
                    ]
                ),
            ]
        );

        $this->assertNull($this->voter->vote($block));
    }
}
