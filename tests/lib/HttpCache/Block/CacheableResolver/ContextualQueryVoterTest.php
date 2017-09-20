<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\CacheableResolver;

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
    private $voter;

    public function setUp()
    {
        $this->voter = new ContextualQueryVoter();
    }

    /**
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

        $this->assertFalse($this->voter->vote(new Block(array('collectionReferences' => array($reference)))));
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

        $this->assertNull($this->voter->vote(new Block(array('collectionReferences' => array($reference)))));
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

        $this->assertNull($this->voter->vote(new Block(array('collectionReferences' => array($reference)))));
    }
}
