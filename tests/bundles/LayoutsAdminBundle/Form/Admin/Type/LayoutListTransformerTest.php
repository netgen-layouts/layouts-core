<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Form\Admin\Type;

use Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\LayoutListTransformer;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LayoutListTransformerTest extends TestCase
{
    private LayoutListTransformer $transformer;

    protected function setUp(): void
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

        self::assertSame($layoutList->getLayouts(), $this->transformer->transform($layoutList));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\LayoutListTransformer::transform
     */
    public function testTransformWithNullValue(): void
    {
        self::assertNull($this->transformer->transform(null));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\LayoutListTransformer::reverseTransform
     */
    public function testReverseTransform(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $layouts = [Layout::fromArray(['id' => $uuid1]), Layout::fromArray(['id' => $uuid2])];

        $transformedLayouts = $this->transformer->reverseTransform($layouts);

        self::assertInstanceOf(LayoutList::class, $transformedLayouts);
        self::assertCount(2, $transformedLayouts);

        self::assertInstanceOf(Layout::class, $transformedLayouts[0]);
        self::assertInstanceOf(Layout::class, $transformedLayouts[1]);

        self::assertSame($uuid1->toString(), $transformedLayouts[0]->getId()->toString());
        self::assertSame($uuid2->toString(), $transformedLayouts[1]->getId()->toString());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\LayoutListTransformer::reverseTransform
     */
    public function testReverseTransformWithNullValue(): void
    {
        self::assertNull($this->transformer->reverseTransform(null));
    }
}
