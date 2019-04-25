<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Layout\Resolver\ConditionType\RouteParameter;
use Netgen\Layouts\Layout\Resolver\TargetType\Route;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Ramsey\Uuid\Uuid;

final class LayoutResolverServiceTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRule
     */
    public function testCreateRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('createRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createRule(new RuleCreateStruct());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRule(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]),
            new RuleUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleMetadata
     */
    public function testUpdateRuleMetadata(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateRuleMetadata')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRuleMetadata(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_PUBLISHED]),
            new RuleMetadataUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('copyRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->copyRule(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Rule::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('ruleExists')
            ->willReturn(false);

        $this->layoutResolverHandler
            ->expects(self::at(2))
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createDraft(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_PUBLISHED]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::discardDraft
     */
    public function testDiscardDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->discardDraft(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->publishRule(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::restoreFromArchive
     */
    public function testRestoreFromArchive(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(2))
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->restoreFromArchive(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Rule::STATUS_ARCHIVED]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::deleteRule
     */
    public function testDeleteRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteRule(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Rule::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::enableRule
     */
    public function testEnableRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(
                PersistenceRule::fromArray(
                    [
                        'layoutId' => 42,
                        'enabled' => false,
                    ]
                )
            );

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateRuleMetadata')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->enableRule(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_PUBLISHED]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::disableRule
     */
    public function testDisableRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(
                PersistenceRule::fromArray(
                    [
                        'enabled' => true,
                    ]
                )
            );

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateRuleMetadata')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->disableRule(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_PUBLISHED]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addTarget
     */
    public function testAddTarget(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('loadRuleTargets')
            ->willReturn([]);

        $this->layoutResolverHandler
            ->expects(self::at(2))
            ->method('addTarget')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $targetCreateStruct = new TargetCreateStruct();
        $targetCreateStruct->type = 'route';

        $this->layoutResolverService->addTarget(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]),
            $targetCreateStruct
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateTarget
     */
    public function testUpdateTarget(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadTarget')
            ->willReturn(new PersistenceTarget());

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateTarget')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateTarget(
            Target::fromArray(['status' => Value::STATUS_DRAFT, 'targetType' => new Route()]),
            new TargetUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::deleteTarget
     */
    public function testDeleteTarget(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadTarget')
            ->willReturn(new PersistenceTarget());

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteTarget')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteTarget(Target::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addCondition
     */
    public function testAddCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('addCondition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->type = 'route_parameter';

        $this->layoutResolverService->addCondition(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]),
            $conditionCreateStruct
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateCondition
     */
    public function testUpdateCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadCondition')
            ->willReturn(new PersistenceCondition());

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateCondition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateCondition(
            Condition::fromArray(['status' => Value::STATUS_DRAFT, 'conditionType' => new RouteParameter()]),
            new ConditionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::deleteCondition
     */
    public function testDeleteCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadCondition')
            ->willReturn(new PersistenceCondition());

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteCondition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteCondition(Condition::fromArray(['status' => Value::STATUS_DRAFT]));
    }
}
