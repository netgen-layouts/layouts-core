<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Form\Admin\Type;

use Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\LayoutListTransformer;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(LayoutListTransformer::class)]
final class LayoutListTransformerTest extends TestCase
{
    private LayoutListTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = new LayoutListTransformer();
    }

    public function testTransform(): void
    {
        $layoutList = new LayoutList();

        self::assertSame($layoutList->getLayouts(), $this->transformer->transform($layoutList));
    }

    public function testTransformWithNullValue(): void
    {
        self::assertNull($this->transformer->transform(null));
    }

    public function testReverseTransform(): void
    {
        $uuid1 = Uuid::v4();
        $uuid2 = Uuid::v4();

        $layouts = [Layout::fromArray(['id' => $uuid1]), Layout::fromArray(['id' => $uuid2])];

        $transformedLayouts = $this->transformer->reverseTransform($layouts);

        self::assertInstanceOf(LayoutList::class, $transformedLayouts);
        self::assertCount(2, $transformedLayouts);

        self::assertInstanceOf(Layout::class, $transformedLayouts[0]);
        self::assertInstanceOf(Layout::class, $transformedLayouts[1]);

        self::assertSame($uuid1->toString(), $transformedLayouts[0]->id->toString());
        self::assertSame($uuid2->toString(), $transformedLayouts[1]->id->toString());
    }

    public function testReverseTransformWithNullValue(): void
    {
        self::assertNull($this->transformer->reverseTransform(null));
    }
}
