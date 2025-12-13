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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

use function sprintf;

#[CoversClass(RuleEntityHandler::class)]
final class RuleEntityHandlerTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleEntityHandler $entityHandler;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->entityHandler = new RuleEntityHandler(
            $this->layoutResolverServiceStub,
            new TargetTypeRegistry([]),
            new ConditionTypeRegistry([]),
        );
    }

    public function testLoadEntity(): void
    {
        $uuid = Uuid::v4();

        $rule = Rule::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceStub
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willReturn($rule);

        self::assertSame($rule, $this->entityHandler->loadEntity($uuid));
    }

    public function testLoadEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::v4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceStub
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule', $uuid->toString()));

        $this->entityHandler->loadEntity($uuid);
    }

    public function testEntityExists(): void
    {
        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
            ->method('ruleExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(true);

        self::assertTrue($this->entityHandler->entityExists($uuid));
    }

    public function testEntityExistsReturnsFalse(): void
    {
        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
            ->method('ruleExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(false);

        self::assertFalse($this->entityHandler->entityExists($uuid));
    }

    public function testDeleteEntity(): void
    {
        $uuid = Uuid::v4();

        $rule = Rule::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceStub
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willReturn($rule);

        $this->layoutResolverServiceStub
            ->method('deleteRule')
            ->with(self::identicalTo($rule));

        $this->entityHandler->deleteEntity($uuid);
    }

    public function testDeleteEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::v4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceStub
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule', $uuid->toString()));

        $this->entityHandler->deleteEntity($uuid);
    }
}
