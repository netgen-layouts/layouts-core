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

#[CoversClass(PolicyToRoleMapVoter::class)]
final class PolicyToRoleMapVoterTest extends TestCase
{
    private PolicyToRoleMapVoter $voter;

    private MockObject $accessDecisionManagerMock;

    protected function setUp(): void
    {
        $this->accessDecisionManagerMock = $this->createMock(AccessDecisionManagerInterface::class);

        $this->voter = new PolicyToRoleMapVoter($this->accessDecisionManagerMock);
    }

    public function testVote(): void
    {
        $token = $this->createMock(TokenInterface::class);

        $this->accessDecisionManagerMock
            ->expects(self::once())
            ->method('decide')
            ->with(self::equalTo($token), self::equalTo(['ROLE_NGLAYOUTS_ADMIN']))
            ->willReturn(true);

        $vote = $this->voter->vote(
            $token,
            null,
            ['nglayouts:layout:add'],
        );

        self::assertSame($vote, $this->voter::ACCESS_GRANTED);
    }

    public function testVoteWithUnsupportedAttribute(): void
    {
        $this->accessDecisionManagerMock
            ->expects(self::never())
            ->method('decide');

        $this->voter->vote($this->createMock(TokenInterface::class), null, [new stdClass()]);
    }

    public function testVoteWithNonExistingRole(): void
    {
        $this->accessDecisionManagerMock
            ->expects(self::never())
            ->method('decide');

        $vote = $this->voter->vote($this->createMock(TokenInterface::class), null, ['nglayouts:unknown:unknown']);
        self::assertSame($vote, $this->voter::ACCESS_DENIED);
    }
}
