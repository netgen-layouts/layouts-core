<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\EntityHandler;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

use function sprintf;

#[CoversClass(RuleEntityHandler::class)]
final class RuleEntityHandlerTest extends TestCase
{
    private MockObject&LayoutResolverService $layoutResolverServiceMock;

    private RuleEntityHandler $entityHandler;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->entityHandler = new RuleEntityHandler(
            $this->layoutResolverServiceMock,
            new TargetTypeRegistry([]),
            new ConditionTypeRegistry([]),
        );
    }

    public function testLoadEntity(): void
    {
        $uuid = Uuid::v7();

        $rule = Rule::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willReturn($rule);

        self::assertSame($rule, $this->entityHandler->loadEntity($uuid));
    }

    public function testLoadEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::v7();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule', $uuid->toString()));

        $this->entityHandler->loadEntity($uuid);
    }

    public function testEntityExists(): void
    {
        $uuid = Uuid::v7();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('ruleExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(true);

        self::assertTrue($this->entityHandler->entityExists($uuid));
    }

    public function testEntityExistsReturnsFalse(): void
    {
        $uuid = Uuid::v7();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('ruleExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(false);

        self::assertFalse($this->entityHandler->entityExists($uuid));
    }

    public function testDeleteEntity(): void
    {
        $uuid = Uuid::v7();

        $rule = Rule::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willReturn($rule);

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('deleteRule')
            ->with(self::identicalTo($rule));

        $this->entityHandler->deleteEntity($uuid);
    }

    public function testDeleteEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::v7();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule', $uuid->toString()));

        $this->entityHandler->deleteEntity($uuid);
    }
}
