<?php

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\Collection\Item\VisibilityResolver;
use Netgen\BlockManager\Core\Values\Collection\Item;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VisibilityResolverTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Item\VisibilityResolver::setVoters
     * @expectedException \Netgen\BlockManager\Exception\InvalidInterfaceException
     * @expectedExceptionMessage Voter "stdClass" needs to implement "Netgen\BlockManager\Collection\Item\VisibilityResolver\VoterInterface" interface.
     */
    public function testSetVotersThrowsInvalidInterfaceException()
    {
        $visibilityResolver = new VisibilityResolver();
        $visibilityResolver->setVoters(array(new stdClass()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\VisibilityResolver::setVoters
     * @covers \Netgen\BlockManager\Collection\Item\VisibilityResolver::isVisible
     *
     * @param \Netgen\BlockManager\Collection\Item\VisibilityResolver\VoterInterface[] $voters
     * @param bool $result
     *
     * @dataProvider isVisibleProvider
     */
    public function testIsVisible(array $voters, $result)
    {
        $visibilityResolver = new VisibilityResolver();
        $visibilityResolver->setVoters($voters);

        $this->assertEquals($result, $visibilityResolver->isVisible(new Item()));
    }

    public function isVisibleProvider()
    {
        return array(
            array(
                array(
                    new VoterStub(VoterStub::ABSTAIN),
                    new VoterStub(VoterStub::ABSTAIN),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::ABSTAIN),
                    new VoterStub(VoterStub::YES),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::ABSTAIN),
                    new VoterStub(VoterStub::NO),
                ),
                false,
            ),
            array(
                array(
                    new VoterStub(VoterStub::YES),
                    new VoterStub(VoterStub::ABSTAIN),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::YES),
                    new VoterStub(VoterStub::YES),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::YES),
                    new VoterStub(VoterStub::NO),
                ),
                true,
            ),
            array(
                array(
                    new VoterStub(VoterStub::NO),
                    new VoterStub(VoterStub::ABSTAIN),
                ),
                false,
            ),
            array(
                array(
                    new VoterStub(VoterStub::NO),
                    new VoterStub(VoterStub::YES),
                ),
                false,
            ),
            array(
                array(
                    new VoterStub(VoterStub::NO),
                    new VoterStub(VoterStub::NO),
                ),
                false,
            ),
        );
    }
}
