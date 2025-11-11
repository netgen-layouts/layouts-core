<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\PlaceholderNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs\NormalizerStub;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

#[CoversClass(PlaceholderNormalizer::class)]
final class PlaceholderNormalizerTest extends TestCase
{
    private PlaceholderNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new PlaceholderNormalizer();
        $this->normalizer->setNormalizer(new Serializer([new NormalizerStub()]));
    }

    public function testNormalize(): void
    {
        $block = new Block();
        $placeholder = Placeholder::fromArray(
            [
                'identifier' => 'main',
                'blocks' => new ArrayCollection([$block]),
            ],
        );

        self::assertSame(
            [
                'identifier' => 'main',
                'blocks' => ['data'],
            ],
            $this->normalizer->normalize(new Value($placeholder)),
        );
    }

    #[DataProvider('supportsNormalizationDataProvider')]
    public function testSupportsNormalization(mixed $data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public static function supportsNormalizationDataProvider(): iterable
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['placeholder', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new Placeholder(), false],
            [new Value(new APIValue()), false],
            [new Value(new Placeholder()), true],
        ];
    }
}
