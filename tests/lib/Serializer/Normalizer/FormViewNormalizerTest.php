<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Serializer\Normalizer\FormViewNormalizer;
use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

final class FormViewNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $viewRendererMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\FormViewNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->viewRendererMock = $this->createMock(RendererInterface::class);

        $this->normalizer = new FormViewNormalizer($this->viewRendererMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\FormViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\FormViewNormalizer::normalize
     */
    public function testNormalize()
    {
        $form = $this->createMock(FormInterface::class);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo($form),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    [
                        'api_version' => 1,
                    ]
                )
            )
            ->will($this->returnValue('rendered form view'));

        $data = $this->normalizer->normalize(new FormView($form, 1));

        $this->assertEquals(['form' => 'rendered form view'], $data);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\FormViewNormalizer::supportsNormalization
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
            [new FormView($this->createMock(FormInterface::class), 1), true],
        ];
    }
}
