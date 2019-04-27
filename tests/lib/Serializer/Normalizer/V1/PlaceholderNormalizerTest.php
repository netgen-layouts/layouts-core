<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Normalizer\V1;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Serializer\Normalizer\V1\PlaceholderNormalizer;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Serializer\Stubs\NormalizerStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

final class PlaceholderNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Serializer\Normalizer\V1\PlaceholderNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new PlaceholderNormalizer();
        $this->normalizer->setNormalizer(new Serializer([new NormalizerStub()]));
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\PlaceholderNormalizer::buildViewValues
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\PlaceholderNormalizer::normalize
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
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\PlaceholderNormalizer::supportsNormalization
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
