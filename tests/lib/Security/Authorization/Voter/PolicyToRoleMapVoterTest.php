<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Security\Authorization\Voter;

use Netgen\Layouts\Security\Authorization\Voter\PolicyToRoleMapVoter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

#[CoversClass(PolicyToRoleMapVoter::class)]
final class PolicyToRoleMapVoterTest extends TestCase
{
    private PolicyToRoleMapVoter $voter;

    private Stub&AccessDecisionManagerInterface $accessDecisionManagerStub;

    protected function setUp(): void
    {
        $this->accessDecisionManagerStub = self::createStub(AccessDecisionManagerInterface::class);

        $this->voter = new PolicyToRoleMapVoter($this->accessDecisionManagerStub);
    }

    public function testVote(): void
    {
        $tokenStub = self::createStub(TokenInterface::class);

        $this->accessDecisionManagerStub
            ->method('decide')
            ->with(self::equalTo($tokenStub), self::equalTo(['ROLE_NGLAYOUTS_ADMIN']))
            ->willReturn(true);

        $vote = $this->voter->vote(
            $tokenStub,
            null,
            ['nglayouts:layout:add'],
        );

        self::assertSame(VoterInterface::ACCESS_GRANTED, $vote);
    }

    public function testVoteWithUnsupportedAttribute(): void
    {
        $vote = $this->voter->vote(self::createStub(TokenInterface::class), null, [new stdClass()]);
        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $vote);
    }

    public function testVoteWithNonExistingRole(): void
    {
        $vote = $this->voter->vote(self::createStub(TokenInterface::class), null, ['nglayouts:unknown:unknown']);
        self::assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }
}
