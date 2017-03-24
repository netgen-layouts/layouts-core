<?php

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\Layout\Form\DataTransformer\LayoutTypeTransformer;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;

class LayoutTypeTransformerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Form\DataTransformer\LayoutTypeTransformer
     */
    protected $dataTransformer;

    public function setUp()
    {
        $layoutTypeRegistry = new LayoutTypeRegistry();
        $layoutTypeRegistry->addLayoutType(new LayoutType(array('identifier' => '4_zones_a')));

        $this->dataTransformer = new LayoutTypeTransformer($layoutTypeRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\DataTransformer\LayoutTypeTransformer::__construct
     * @covers \Netgen\BlockManager\Layout\Form\DataTransformer\LayoutTypeTransformer::transform
     */
    public function testTransform()
    {
        $this->assertEquals(
            '4_zones_a',
            $this->dataTransformer->transform(
                new LayoutType(array('identifier' => '4_zones_a'))
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\DataTransformer\LayoutTypeTransformer::transform
     */
    public function testTransformNullValue()
    {
        $this->assertEquals('', $this->dataTransformer->transform(null));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\DataTransformer\LayoutTypeTransformer::transform
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage Provided value is not a layout type.
     */
    public function testTransformThrowsException()
    {
        $this->dataTransformer->transform('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\DataTransformer\LayoutTypeTransformer::reverseTransform
     */
    public function testReverseTransform()
    {
        $this->assertEquals(
            new LayoutType(array('identifier' => '4_zones_a')),
            $this->dataTransformer->reverseTransform('4_zones_a')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\DataTransformer\LayoutTypeTransformer::reverseTransform
     */
    public function testReverseTransformEmptyString()
    {
        $this->assertNull($this->dataTransformer->reverseTransform(''));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\DataTransformer\LayoutTypeTransformer::reverseTransform
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage Provided value is not a layout type.
     */
    public function testReverseTransformThrowsException()
    {
        $this->dataTransformer->reverseTransform('unknown');
    }
}
