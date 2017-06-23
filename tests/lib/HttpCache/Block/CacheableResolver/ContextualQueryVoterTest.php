<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualQueryVoter;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class ContextualQueryVoterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualQueryVoter
     */
    protected $voter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->voter = new ContextualQueryVoter($this->blockServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualQueryVoter::__construct
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualQueryVoter::vote
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualQueryVoter::hasContextualQuery
     */
    public function testVote()
    {
        $reference = new CollectionReference(
            array(
                'collection' => new Collection(
                    array(
                        'type' => Collection::TYPE_DYNAMIC,
                        'query' => new Query(
                            array(
                                'queryType' => new QueryType('type', array(), null, true),
                            )
                        ),
                    )
                ),
            )
        );

        $this->blockServiceMock
            ->expects($this->at(0))
            ->method('loadCollectionReferences')
            ->with($this->equalTo(new Block()))
            ->will($this->returnValue(array($reference)));

        $this->assertFalse($this->voter->vote(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualQueryVoter::vote
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualQueryVoter::hasContextualQuery
     */
    public function testVoteWithNonContextualQuery()
    {
        $reference = new CollectionReference(
            array(
                'collection' => new Collection(
                    array(
                        'type' => Collection::TYPE_DYNAMIC,
                        'query' => new Query(
                            array(
                                'queryType' => new QueryType('type'),
                            )
                        ),
                    )
                ),
            )
        );

        $this->blockServiceMock
            ->expects($this->at(0))
            ->method('loadCollectionReferences')
            ->with($this->equalTo(new Block()))
            ->will($this->returnValue(array($reference)));

        $this->assertNull($this->voter->vote(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualQueryVoter::vote
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver\ContextualQueryVoter::hasContextualQuery
     */
    public function testVoteWithNoQuery()
    {
        $reference = new CollectionReference(
            array(
                'collection' => new Collection(
                    array(
                        'type' => Collection::TYPE_MANUAL,
                    )
                ),
            )
        );

        $this->blockServiceMock
            ->expects($this->at(0))
            ->method('loadCollectionReferences')
            ->with($this->equalTo(new Block()))
            ->will($this->returnValue(array($reference)));

        $this->assertNull($this->voter->vote(new Block()));
    }
}
