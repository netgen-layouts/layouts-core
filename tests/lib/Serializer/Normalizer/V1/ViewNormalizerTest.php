<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\Serializer\Stubs\SerializerInterface;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;

final class ViewNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $viewRendererMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $normalizerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->viewRendererMock = $this->createMock(RendererInterface::class);
        $this->normalizerMock = $this->createMock(SerializerInterface::class);

        $this->normalizer = new ViewNormalizer($this->viewRendererMock);
        $this->normalizer->setSerializer($this->normalizerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer::setSerializer
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $value = new Value();
        $this->normalizerMock
            ->expects(self::once())
            ->method('normalize')
            ->with(self::equalTo(new VersionedValue($value, 1)))
            ->willReturn(['id' => 42]);

        $this->viewRendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($value),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(['api_version' => 1])
            )
            ->willReturn('rendered view');

        $view = new View($value, 1);

        $data = $this->normalizer->normalize($view);

        self::assertSame(['id' => 42, 'html' => 'rendered view'], $data);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer::setSerializer
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::normalize
     */
    public function testNormalizeWithoutRendering(): void
    {
        $value = new Value();
        $this->normalizerMock
            ->expects(self::once())
            ->method('normalize')
            ->with(self::equalTo(new VersionedValue($value, 1)))
            ->willReturn(['id' => 42]);

        $this->viewRendererMock
            ->expects(self::never())
            ->method('renderValue');

        $view = new View($value, 1);

        $data = $this->normalizer->normalize($view, null, ['disable_html' => true]);

        self::assertSame(['id' => 42], $data);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer::setSerializer
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::normalize
     */
    public function testNormalizeWithInvalidDisableRenderingValue(): void
    {
        $value = new Value();

        $this->normalizerMock
            ->expects(self::once())
            ->method('normalize')
            ->with(self::equalTo(new VersionedValue($value, 1)))
            ->willReturn(['id' => 42]);

        $this->viewRendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($value),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(['api_version' => 1])
            )
            ->willReturn('rendered view');

        $view = new View($value, 1);

        $data = $this->normalizer->normalize($view, null, ['disable_html' => 'true']);

        self::assertSame(['id' => 42, 'html' => 'rendered view'], $data);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::supportsNormalization
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
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new Block(), false],
            [new VersionedValue(new Block(), 1), false],
            [new View(new Block(), 1), true],
        ];
    }
}
