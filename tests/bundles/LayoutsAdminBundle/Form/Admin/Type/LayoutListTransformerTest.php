<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Form\Admin\Type;

use Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\LayoutListTransformer;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use PHPUnit\Framework\TestCase;

final class LayoutListTransformerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\LayoutListTransformer
     */
    private $transformer;

    public function setUp(): void
    {
        parent::setUp();

        $this->transformer = new LayoutListTransformer();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\LayoutListTransformer::transform
     */
    public function testTransform(): void
    {
        $layoutList = new LayoutList();

        self::assertSame($layoutList, $this->transformer->transform($layoutList));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\LayoutListTransformer::reverseTransform
     */
    public function testReverseTransform(): void
    {
        $layouts = [Layout::fromArray(['id' => 42]), Layout::fromArray(['id' => 24])];

        $transformedLayouts = $this->transformer->reverseTransform($layouts);

        self::assertInstanceOf(LayoutList::class, $transformedLayouts);
        self::assertCount(2, $transformedLayouts);
        self::assertSame(42, $transformedLayouts[0]->getId());
        self::assertSame(24, $transformedLayouts[1]->getId());
    }
}
