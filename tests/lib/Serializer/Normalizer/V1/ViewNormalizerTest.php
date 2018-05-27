<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

final class ViewNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $viewRendererMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->viewRendererMock = $this->createMock(RendererInterface::class);
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new ViewNormalizer($this->viewRendererMock);
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::normalize
     */
    public function testNormalize()
    {
        $this->serializerMock
            ->expects($this->once())
            ->method('normalize')
            ->with($this->equalTo(new VersionedValue(new Value(), 1)))
            ->will($this->returnValue(['id' => 42]));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Value()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(['api_version' => 1])
            )
            ->will($this->returnValue('rendered view'));

        $view = new View(new Value(), 1);

        $data = $this->normalizer->normalize($view);

        $this->assertEquals(['id' => 42, 'html' => 'rendered view'], $data);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ViewNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $this->assertEquals($expected, $this->normalizer->supportsNormalization($data));
    }

    /**
     * Provider for {@link self::testSupportsNormalization}.
     *
     * @return array
     */
    public function supportsNormalizationProvider()
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
