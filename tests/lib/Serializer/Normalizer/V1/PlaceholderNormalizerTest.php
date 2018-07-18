<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PlaceholderNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $normalizerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new PlaceholderNormalizer();
        $this->normalizer->setNormalizer($this->normalizerMock);
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

        $this->normalizerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with($this->equalTo([new View($block, 1)]))
            ->will($this->returnValue(['normalized blocks']));

        $this->assertSame(
            [
                'identifier' => 'main',
                'blocks' => ['normalized blocks'],
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
        $this->assertSame($expected, $this->normalizer->supportsNormalization($data));
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
