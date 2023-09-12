<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Item\VisibilityResolver;
use PHPUnit\Framework\TestCase;

final class VisibilityResolverTest extends TestCase
{
    /**
     * @param \Netgen\Layouts\Collection\Item\VisibilityVoterInterface[] $voters
     *
     * @covers \Netgen\Layouts\Collection\Item\VisibilityResolver::__construct
     * @covers \Netgen\Layouts\Collection\Item\VisibilityResolver::isVisible
     *
     * @dataProvider isVisibleDataProvider
     */
    public function testIsVisible(array $voters, bool $result): void
    {
        $visibilityResolver = new VisibilityResolver($voters);

        self::assertSame($result, $visibilityResolver->isVisible(new Item()));
    }

    public static function isVisibleDataProvider(): iterable
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
