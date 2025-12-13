<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\TargetValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(TargetValueResolver::class)]
final class TargetValueResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private TargetValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->valueResolver = new TargetValueResolver($this->layoutResolverServiceStub);
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

        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
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

        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
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
