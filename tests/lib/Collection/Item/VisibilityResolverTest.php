<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Item\VisibilityResolver;
use Netgen\Layouts\Collection\Item\VisibilityVoterResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(VisibilityResolver::class)]
final class VisibilityResolverTest extends TestCase
{
    /**
     * @param \Netgen\Layouts\Collection\Item\VisibilityVoterInterface[] $voters
     */
    #[DataProvider('isVisibleDataProvider')]
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
                    new VoterStub(VisibilityVoterResult::Abstain),
                    new VoterStub(VisibilityVoterResult::Abstain),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VisibilityVoterResult::Abstain),
                    new VoterStub(VisibilityVoterResult::Yes),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VisibilityVoterResult::Abstain),
                    new VoterStub(VisibilityVoterResult::No),
                ],
                false,
            ],
            [
                [
                    new VoterStub(VisibilityVoterResult::Yes),
                    new VoterStub(VisibilityVoterResult::Abstain),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VisibilityVoterResult::Yes),
                    new VoterStub(VisibilityVoterResult::Yes),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VisibilityVoterResult::Yes),
                    new VoterStub(VisibilityVoterResult::No),
                ],
                true,
            ],
            [
                [
                    new VoterStub(VisibilityVoterResult::No),
                    new VoterStub(VisibilityVoterResult::Abstain),
                ],
                false,
            ],
            [
                [
                    new VoterStub(VisibilityVoterResult::No),
                    new VoterStub(VisibilityVoterResult::Yes),
                ],
                false,
            ],
            [
                [
                    new VoterStub(VisibilityVoterResult::No),
                    new VoterStub(VisibilityVoterResult::No),
                ],
                false,
            ],
        ];
    }
}
