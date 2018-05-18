<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver;
use PHPUnit\Framework\TestCase;

final class CacheableResolverTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver::isCacheable
     * @covers \Netgen\BlockManager\HttpCache\Block\CacheableResolver::setVoters
     *
     * @param \Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface[] $voters
     * @param bool $result
     *
     * @dataProvider isCacheableProvider
     */
    public function testIsCacheable(array $voters, $result)
    {
        $cacheableResolver = new CacheableResolver();
        $cacheableResolver->setVoters($voters);

        $this->assertEquals($result, $cacheableResolver->isCacheable(new Block()));
    }

    public function isCacheableProvider()
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
