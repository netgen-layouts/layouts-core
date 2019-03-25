<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Security\Authorization\Voter;

use Netgen\BlockManager\Exception\Security\PolicyException;
use Netgen\BlockManager\Security\Authorization\Voter\PolicyToRoleMapVoter;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class PolicyToRoleMapVoterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Security\Authorization\Voter\PolicyToRoleMapVoter
     */
    private $voter;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $authorizationCheckerMock;

    public function setUp(): void
    {
        $this->authorizationCheckerMock = $this->createMock(AuthorizationCheckerInterface::class);

        $this->voter = new PolicyToRoleMapVoter($this->authorizationCheckerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Security\Authorization\Voter\PolicyToRoleMapVoter::__construct
     * @covers \Netgen\BlockManager\Security\Authorization\Voter\PolicyToRoleMapVoter::supports
     * @covers \Netgen\BlockManager\Security\Authorization\Voter\PolicyToRoleMapVoter::voteOnAttribute
     */
    public function testVote(): void
    {
        $this->authorizationCheckerMock
            ->expects(self::once())
            ->method('isGranted')
            ->with(self::equalTo('ROLE_NGBM_ADMIN'))
            ->will(self::returnValue(true));

        $vote = $this->voter->vote(
            $this->createMock(TokenInterface::class),
            null,
            ['nglayouts:layout:add']
        );

        self::assertSame($vote, $this->voter::ACCESS_GRANTED);
    }

    /**
     * @covers \Netgen\BlockManager\Security\Authorization\Voter\PolicyToRoleMapVoter::supports
     */
    public function testVoteWithUnsupportedAttribute(): void
    {
        $this->authorizationCheckerMock
            ->expects(self::never())
            ->method('isGranted');

        $this->voter->vote($this->createMock(TokenInterface::class), null, [new stdClass()]);
    }

    /**
     * @covers \Netgen\BlockManager\Security\Authorization\Voter\PolicyToRoleMapVoter::supports
     * @covers \Netgen\BlockManager\Security\Authorization\Voter\PolicyToRoleMapVoter::voteOnAttribute
     */
    public function testVoteWithNonExistingRole(): void
    {
        $this->authorizationCheckerMock
            ->expects(self::never())
            ->method('isGranted');

        $this->expectException(PolicyException::class);
        $this->expectExceptionMessage('Policy "nglayouts:unknown:unknown" is not supported.');

        $this->voter->vote($this->createMock(TokenInterface::class), null, ['nglayouts:unknown:unknown']);
    }
}
