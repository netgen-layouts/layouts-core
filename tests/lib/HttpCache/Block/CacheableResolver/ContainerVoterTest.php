<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter;
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
    protected $blockServiceMock;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->blockServiceMock
            ->expects($this->any())
            ->method('loadCollectionReferences')
            ->will($this->returnValue(array()));

        $this->voter = new ContainerVoter($this->blockServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::vote
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::containerHasTwigBlock
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
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::vote
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContainerVoter::containerHasTwigBlock
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
