<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Security\Authorization\Voter;

use Netgen\Layouts\Security\Authorization\Voter\PolicyToRoleMapVoter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

#[CoversClass(PolicyToRoleMapVoter::class)]
final class PolicyToRoleMapVoterTest extends TestCase
{
    private PolicyToRoleMapVoter $voter;

    private MockObject&AccessDecisionManagerInterface $accessDecisionManagerMock;

    protected function setUp(): void
    {
        $this->accessDecisionManagerMock = $this->createMock(AccessDecisionManagerInterface::class);

        $this->voter = new PolicyToRoleMapVoter($this->accessDecisionManagerMock);
    }

    public function testVote(): void
    {
        $tokenMock = $this->createMock(TokenInterface::class);

        $this->accessDecisionManagerMock
            ->expects($this->once())
            ->method('decide')
            ->with(self::equalTo($tokenMock), self::equalTo(['ROLE_NGLAYOUTS_ADMIN']))
            ->willReturn(true);

        $vote = $this->voter->vote(
            $tokenMock,
            null,
            ['nglayouts:layout:add'],
        );

        self::assertSame(VoterInterface::ACCESS_GRANTED, $vote);
    }

    public function testVoteWithUnsupportedAttribute(): void
    {
        $this->accessDecisionManagerMock
            ->expects($this->never())
            ->method('decide');

        $this->voter->vote($this->createMock(TokenInterface::class), null, [new stdClass()]);
    }

    public function testVoteWithNonExistingRole(): void
    {
        $this->accessDecisionManagerMock
            ->expects($this->never())
            ->method('decide');

        $vote = $this->voter->vote($this->createMock(TokenInterface::class), null, ['nglayouts:unknown:unknown']);
        self::assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }
}
