<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ViewNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use Netgen\Layouts\View\RendererInterface;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ViewNormalizerTest extends TestCase
{
    private MockObject $viewRendererMock;

    private MockObject $normalizerMock;

    private ViewNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->viewRendererMock = $this->createMock(RendererInterface::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new ViewNormalizer($this->viewRendererMock);
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ViewNormalizer::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ViewNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $value = new APIValue();
        $this->normalizerMock
            ->expects(self::once())
            ->method('normalize')
            ->with(self::equalTo(new Value($value)))
            ->willReturn(['id' => 42]);

        $this->viewRendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($value),
                self::identicalTo(ViewInterface::CONTEXT_APP),
            )
            ->willReturn('rendered view');

        $view = new View($value);

        $data = $this->normalizer->normalize($view);

        self::assertSame(['id' => 42, 'html' => 'rendered view'], $data);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ViewNormalizer::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ViewNormalizer::normalize
     */
    public function testNormalizeWithoutRendering(): void
    {
        $value = new APIValue();
        $this->normalizerMock
            ->expects(self::once())
            ->method('normalize')
            ->with(self::equalTo(new Value($value)))
            ->willReturn(['id' => 42]);

        $this->viewRendererMock
            ->expects(self::never())
            ->method('renderValue');

        $view = new View($value);

        $data = $this->normalizer->normalize($view, null, ['disable_html' => true]);

        self::assertSame(['id' => 42], $data);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ViewNormalizer::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ViewNormalizer::normalize
     */
    public function testNormalizeWithInvalidDisableRenderingValue(): void
    {
        $value = new APIValue();

        $this->normalizerMock
            ->expects(self::once())
            ->method('normalize')
            ->with(self::equalTo(new Value($value)))
            ->willReturn(['id' => 42]);

        $this->viewRendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($value),
                self::identicalTo(ViewInterface::CONTEXT_APP),
            )
            ->willReturn('rendered view');

        $view = new View($value);

        $data = $this->normalizer->normalize($view, null, ['disable_html' => 'true']);

        self::assertSame(['id' => 42, 'html' => 'rendered view'], $data);
    }

    /**
     * @param mixed $data
     *
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ViewNormalizer::supportsNormalization
     *
     * @dataProvider supportsNormalizationDataProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

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
            [new APIValue(), false],
            [new Block(), false],
            [new Value(new Block()), false],
            [new View(new Block()), true],
        ];
    }
}
