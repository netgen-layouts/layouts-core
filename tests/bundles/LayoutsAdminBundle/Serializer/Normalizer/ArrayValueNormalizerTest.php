<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ArrayValueNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\ArrayValue;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Tests\API\Stubs\Value as StubValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[CoversClass(ArrayValueNormalizer::class)]
final class ArrayValueNormalizerTest extends TestCase
{
    private Stub&NormalizerInterface $normalizerStub;

    private ArrayValueNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizerStub = self::createStub(NormalizerInterface::class);

        $this->normalizer = new ArrayValueNormalizer();
        $this->normalizer->setNormalizer($this->normalizerStub);
    }

    public function testNormalize(): void
    {
        $value = new StubValue();
        $this->normalizerStub
            ->method('normalize')
            ->with(
                self::identicalTo($value),
                self::identicalTo('json'),
                self::identicalTo(['some' => 'context']),
            )
            ->willReturn(['key' => 'serialized']);

        $data = $this->normalizer->normalize(new Value($value), 'json', ['some' => 'context']);

        self::assertSame(['key' => 'serialized'], $data);
    }

    #[DataProvider('supportsNormalizationDataProvider')]
    public function testSupportsNormalization(mixed $data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    /**
     * @return iterable<mixed>
     */
    public static function supportsNormalizationDataProvider(): iterable
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new StubValue(), false],
            [new Block(), false],
            [new Value(new Block()), false],
            [new ArrayValue([new Block()]), true],
        ];
    }
}
