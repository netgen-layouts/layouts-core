<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\EntityHandler;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

use function sprintf;

final class RuleEntityHandlerTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

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

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler::__construct
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler::loadEntity
     */
    public function testLoadEntity(): void
    {
        $uuid = Uuid::uuid4();

        $rule = Rule::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willReturn($rule);

        self::assertSame($rule, $this->entityHandler->loadEntity($uuid));
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler::loadEntity
     */
    public function testLoadEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::uuid4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule', $uuid->toString()));

        $this->entityHandler->loadEntity($uuid);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler::entityExists
     */
    public function testEntityExists(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('ruleExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(true);

        self::assertTrue($this->entityHandler->entityExists($uuid));
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler::entityExists
     */
    public function testEntityExistsReturnsFalse(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('ruleExists')
            ->with(self::identicalTo($uuid))
            ->willReturn(false);

        self::assertFalse($this->entityHandler->entityExists($uuid));
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler::deleteEntity
     */
    public function testDeleteEntity(): void
    {
        $uuid = Uuid::uuid4();

        $rule = Rule::fromArray(['id' => $uuid]);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willReturn($rule);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('deleteRule')
            ->with(self::identicalTo($rule));

        $this->entityHandler->deleteEntity($uuid);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler::deleteEntity
     */
    public function testDeleteEntityWithNonExistentEntity(): void
    {
        $uuid = Uuid::uuid4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Could not find rule with identifier "%s"', $uuid->toString()));

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRule')
            ->with(self::identicalTo($uuid))
            ->willThrowException(new NotFoundException('rule', $uuid->toString()));

        $this->layoutResolverServiceMock
            ->expects(self::never())
            ->method('deleteRule');

        $this->entityHandler->deleteEntity($uuid);
    }
}
