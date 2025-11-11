<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\TargetValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(TargetValueResolver::class)]
final class TargetValueResolverTest extends TestCase
{
    private MockObject&LayoutResolverService $layoutResolverServiceMock;

    private TargetValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->valueResolver = new TargetValueResolver($this->layoutResolverServiceMock);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['targetId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('target', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(Target::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $target = new Target();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadTarget')
            ->with(self::equalTo($uuid))
            ->willReturn($target);

        self::assertSame(
            $target,
            $this->valueResolver->loadValue(
                [
                    'targetId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $target = new Target();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadTargetDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($target);

        self::assertSame(
            $target,
            $this->valueResolver->loadValue(
                [
                    'targetId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
