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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

use function sprintf;

final class RuleGroupEntityHandlerTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private RuleGroupEntityHandler $entityHandler;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->entityHandler = new RuleGroupEntityHandler(
            $this->layoutResolverServiceMock,
            new RuleEntityHandler(
                $this->layoutResolverServiceMock,
                new TargetTypeRegistry([]),
                new ConditionTypeRegistry([]),
            ),
            new ConditionTypeRegistry([]),
        );
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler::__construct
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler::loadEntity
     */
    public function testLoadEntity(): void
    {
        $uuid = Uuid::uuid4();

        $ruleGroup = RuleGroup::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroup')
            ->with(self::identicalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame($ruleGroup, $this->entityHandler->loadEntity($uuid));
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler::loadEntity
     */
    public function testLoadEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::uuid4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule group with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroup')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule group', $uuid->toString()));

        $this->entityHandler->loadEntity($uuid);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler::entityExists
     */
    public function testEntityExists(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('ruleGroupExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(true);

        self::assertTrue($this->entityHandler->entityExists($uuid));
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler::entityExists
     */
    public function testEntityExistsReturnsFalse(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('ruleGroupExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(false);

        self::assertFalse($this->entityHandler->entityExists($uuid));
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler::deleteEntity
     */
    public function testDeleteEntity(): void
    {
        $uuid = Uuid::uuid4();

        $ruleGroup = RuleGroup::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroup')
            ->with(self::identicalTo($uuid))
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('deleteRuleGroup')
            ->with(self::identicalTo($ruleGroup));

        $this->entityHandler->deleteEntity($uuid);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler::deleteEntity
     */
    public function testDeleteEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::uuid4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule group with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroup')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule group', $uuid->toString()));

        $this->layoutResolverServiceMock
            ->expects(self::never())
            ->method('deleteRuleGroup');

        $this->entityHandler->deleteEntity($uuid);
    }
}
