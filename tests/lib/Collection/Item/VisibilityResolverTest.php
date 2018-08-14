<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityResolver;
use PHPUnit\Framework\TestCase;

final class VisibilityResolverTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Item\VisibilityResolver::isVisible
     * @covers \Netgen\BlockManager\Collection\Item\VisibilityResolver::setVoters
     *
     * @dataProvider isVisibleProvider
     */
    public function testIsVisible(array $voters, bool $result): void
    {
        $VisibilityResolver = new VisibilityResolver();
        $VisibilityResolver->setVoters($voters);

        self::assertSame($result, $VisibilityResolver->isVisible(new Item()));
    }

    public function isVisibleProvider(): array
    {
        return [
            [
                [
                    new VoterStub(VoterStub::ABSTAIN),
                    new VoterStub(VoterStub::ABSTAIN),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VoterStub::ABSTAIN),
                    new VoterStub(VoterStub::YES),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VoterStub::ABSTAIN),
                    new VoterStub(VoterStub::NO),
                ],
                false,
            ],
            [
                [
                    new VoterStub(VoterStub::YES),
                    new VoterStub(VoterStub::ABSTAIN),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VoterStub::YES),
                    new VoterStub(VoterStub::YES),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VoterStub::YES),
                    new VoterStub(VoterStub::NO),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VoterStub::NO),
                    new VoterStub(VoterStub::ABSTAIN),
                ],
                false,
            ],
            [
                [
                    new VoterStub(VoterStub::NO),
                    new VoterStub(VoterStub::YES),
                ],
                false,
            ],
            [
                [
                    new VoterStub(VoterStub::NO),
                    new VoterStub(VoterStub::NO),
                ],
                false,
            ],
        ];
    }
}
