<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Serializer\Normalizer\ViewNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\RendererInterface;
use Symfony\Component\Serializer\Serializer;
use PHPUnit\Framework\TestCase;

class ViewNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewRendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\ViewNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->viewRendererMock = $this->createMock(RendererInterface::class);
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new ViewNormalizer($this->viewRendererMock);
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ViewNormalizer::normalize
     */
    public function testNormalize()
    {
        $this->serializerMock
            ->expects($this->once())
            ->method('normalize')
            ->with($this->equalTo(new VersionedValue(new Value(), 1)))
            ->will($this->returnValue(array('id' => 42)));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Value()),
                $this->equalTo(
                    array(
                        'api_version' => 1,
                        'param' => 'value',
                    )
                ),
                $this->equalTo(ViewInterface::CONTEXT_API)
            )
            ->will($this->returnValue('rendered view'));

        $view = new View(new Value(), 1);
        $view->setViewParameters(array('param' => 'value'));

        $data = $this->normalizer->normalize($view);

        self::assertEquals(array('id' => 42, 'html' => 'rendered view'), $data);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ViewNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        self::assertEquals($expected, $this->normalizer->supportsNormalization($data));
    }

    /**
     * Provider for {@link self::testSupportsNormalization}.
     *
     * @return array
     */
    public function supportsNormalizationProvider()
    {
        return array(
            array(null, false),
            array(true, false),
            array(false, false),
            array('block', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new Block(), false),
            array(new VersionedValue(new Block(), 1), false),
            array(new View(new Block(), 1), true),
        );
    }
}
