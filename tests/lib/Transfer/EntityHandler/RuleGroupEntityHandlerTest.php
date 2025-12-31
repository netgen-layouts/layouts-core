<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\EntityHandler;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler;
use Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

use function sprintf;

#[CoversClass(RuleGroupEntityHandler::class)]
final class RuleGroupEntityHandlerTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleGroupEntityHandler $entityHandler;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->entityHandler = new RuleGroupEntityHandler(
            $this->layoutResolverServiceStub,
            new RuleEntityHandler(
                $this->layoutResolverServiceStub,
                new TargetTypeRegistry([]),
                new ConditionTypeRegistry([]),
            ),
            new ConditionTypeRegistry([]),
        );
    }

    public function testLoadEntity(): void
    {
        $uuid = Uuid::v7();

        $ruleGroup = RuleGroup::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->with(self::identicalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame($ruleGroup, $this->entityHandler->loadEntity($uuid));
    }

    public function testLoadEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::v7();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule group with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule group', $uuid->toString()));

        $this->entityHandler->loadEntity($uuid);
    }

    public function testEntityExists(): void
    {
        $uuid = Uuid::v7();

        $this->layoutResolverServiceStub
            ->method('ruleGroupExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(true);

        self::assertTrue($this->entityHandler->entityExists($uuid));
    }

    public function testEntityExistsReturnsFalse(): void
    {
        $uuid = Uuid::v7();

        $this->layoutResolverServiceStub
            ->method('ruleGroupExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(false);

        self::assertFalse($this->entityHandler->entityExists($uuid));
    }

    public function testDeleteEntity(): void
    {
        $uuid = Uuid::v7();

        $ruleGroup = RuleGroup::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->with(self::identicalTo($uuid))
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('deleteRuleGroup')
            ->with(self::identicalTo($ruleGroup));

        $this->entityHandler->deleteEntity($uuid);
    }

    public function testDeleteEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::v7();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule group with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule group', $uuid->toString()));

        $this->entityHandler->deleteEntity($uuid);
    }
}
