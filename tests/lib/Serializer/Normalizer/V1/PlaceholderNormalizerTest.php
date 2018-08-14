<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\Serializer\Stubs\NormalizerStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

final class PlaceholderNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizer = new PlaceholderNormalizer();
        $this->normalizer->setNormalizer(new Serializer([new NormalizerStub()]));
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer::setNormalizer
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $block = new Block();
        $placeholder = Placeholder::fromArray(
            [
                'identifier' => 'main',
                'blocks' => new ArrayCollection([$block]),
            ]
        );

        self::assertSame(
            [
                'identifier' => 'main',
                'blocks' => ['data'],
            ],
            $this->normalizer->normalize(new VersionedValue($placeholder, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public function supportsNormalizationProvider(): array
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['placeholder', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new Placeholder(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Placeholder(), 2), false],
            [new VersionedValue(new Placeholder(), 1), true],
        ];
    }
}
