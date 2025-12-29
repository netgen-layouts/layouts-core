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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[CoversClass(ViewNormalizer::class)]
final class ViewNormalizerTest extends TestCase
{
    private Stub&RendererInterface $viewRendererStub;

    private Stub&NormalizerInterface $normalizerStub;

    private ViewNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->viewRendererStub = self::createStub(RendererInterface::class);
        $this->normalizerStub = self::createStub(NormalizerInterface::class);

        $this->normalizer = new ViewNormalizer($this->viewRendererStub);
        $this->normalizer->setNormalizer($this->normalizerStub);
    }

    public function testNormalize(): void
    {
        $value = new APIValue();
        $this->normalizerStub
            ->method('normalize')
            ->with(self::equalTo(new Value($value)))
            ->willReturn(['id' => 42]);

        $this->viewRendererStub
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

    public function testNormalizeWithoutRendering(): void
    {
        $value = new APIValue();
        $this->normalizerStub
            ->method('normalize')
            ->with(self::equalTo(new Value($value)))
            ->willReturn(['id' => 42]);

        $view = new View($value);

        $data = $this->normalizer->normalize($view, null, ['disable_html' => true]);

        self::assertSame(['id' => 42], $data);
    }

    public function testNormalizeWithInvalidDisableRenderingValue(): void
    {
        $value = new APIValue();

        $this->normalizerStub
            ->method('normalize')
            ->with(self::equalTo(new Value($value)))
            ->willReturn(['id' => 42]);

        $this->viewRendererStub
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
            [new APIValue(), false],
            [new Block(), false],
            [new Value(new Block()), false],
            [new View(new Block()), true],
        ];
    }
}
