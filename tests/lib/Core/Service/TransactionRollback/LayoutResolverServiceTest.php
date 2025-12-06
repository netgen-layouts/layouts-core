<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Core\Service\LayoutResolverService;
use Netgen\Layouts\Layout\Resolver\ConditionType\RouteParameter;
use Netgen\Layouts\Layout\Resolver\TargetType\Route;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition as PersistenceRuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup as PersistenceRuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition as PersistenceRuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Ramsey\Uuid\Uuid;

#[CoversClass(LayoutResolverService::class)]
final class LayoutResolverServiceTest extends TestCase
{
    public function testCreateRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(new PersistenceRuleGroup());

        $this->layoutResolverHandler
            ->method('createRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createRule(
            new RuleCreateStruct(),
            RuleGroup::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Status::Published,
                ],
            ),
        );
    }

    public function testUpdateRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('updateRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRule(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]),
            new RuleUpdateStruct(),
        );
    }

    public function testUpdateRuleMetadata(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('updateRuleMetadata')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRuleMetadata(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]),
            new RuleMetadataUpdateStruct(),
        );
    }

    public function testCopyRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('copyRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->copyRule(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]),
            RuleGroup::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Status::Published,
                ],
            ),
        );
    }

    public function testMoveRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('moveRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->moveRule(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]),
            RuleGroup::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Status::Published,
                ],
            ),
        );
    }

    public function testCreateRuleDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('ruleExists')
            ->willReturn(false);

        $this->layoutResolverHandler
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createRuleDraft(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]));
    }

    public function testDiscardRuleDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->discardRuleDraft(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]));
    }

    public function testPublishRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->publishRule(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]));
    }

    public function testRestoreRuleFromArchive(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42, 'status' => PersistenceStatus::Archived]));

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42, 'status' => PersistenceStatus::Draft]));

        $this->layoutResolverHandler
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->restoreRuleFromArchive(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Archived]));
    }

    public function testDeleteRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('deleteRule')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteRule(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]));
    }

    public function testEnableRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(
                PersistenceRule::fromArray(
                    [
                        'layoutUuid' => Uuid::uuid4()->toString(),
                        'isEnabled' => false,
                    ],
                ),
            );

        $this->layoutResolverHandler
            ->method('updateRuleMetadata')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->enableRule(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]));
    }

    public function testDisableRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(
                PersistenceRule::fromArray(
                    [
                        'isEnabled' => true,
                    ],
                ),
            );

        $this->layoutResolverHandler
            ->method('updateRuleMetadata')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->disableRule(Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]));
    }

    public function testCreateRuleGroup(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(new PersistenceRuleGroup());

        $this->layoutResolverHandler
            ->method('createRuleGroup')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $createStruct = new RuleGroupCreateStruct();
        $createStruct->name = 'Test group';

        $this->layoutResolverService->createRuleGroup(
            $createStruct,
            RuleGroup::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Status::Published,
                ],
            ),
        );
    }

    public function testUpdateRuleGroup(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('updateRuleGroup')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRuleGroup(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]),
            new RuleGroupUpdateStruct(),
        );
    }

    public function testUpdateRuleGroupMetadata(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('updateRuleGroupMetadata')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRuleGroupMetadata(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]),
            new RuleGroupMetadataUpdateStruct(),
        );
    }

    public function testCopyRuleGroup(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('copyRuleGroup')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->copyRuleGroup(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]),
            RuleGroup::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Status::Published,
                ],
            ),
        );
    }

    public function testMoveRuleGroup(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('moveRuleGroup')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->moveRuleGroup(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]),
            RuleGroup::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Status::Published,
                ],
            ),
        );
    }

    public function testCreateRuleGroupDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('ruleGroupExists')
            ->willReturn(false);

        $this->layoutResolverHandler
            ->method('deleteRuleGroup')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createRuleGroupDraft(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]),
        );
    }

    public function testDiscardRuleGroupDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('deleteRuleGroup')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->discardRuleGroupDraft(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]),
        );
    }

    public function testPublishRuleGroup(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('deleteRuleGroup')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->publishRuleGroup(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]),
        );
    }

    public function testRestoreRuleGroupFromArchive(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42, 'status' => PersistenceStatus::Archived]));

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42, 'status' => PersistenceStatus::Draft]));

        $this->layoutResolverHandler
            ->method('deleteRuleGroup')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->restoreRuleGroupFromArchive(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Archived]),
        );
    }

    public function testDeleteRuleGroup(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('deleteRuleGroup')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteRuleGroup(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]),
        );
    }

    public function testEnableRuleGroup(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(
                PersistenceRuleGroup::fromArray(
                    [
                        'isEnabled' => false,
                    ],
                ),
            );

        $this->layoutResolverHandler
            ->method('updateRuleGroupMetadata')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->enableRuleGroup(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]),
        );
    }

    public function testDisableRuleGroup(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(
                PersistenceRuleGroup::fromArray(
                    [
                        'isEnabled' => true,
                    ],
                ),
            );

        $this->layoutResolverHandler
            ->method('updateRuleGroupMetadata')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->disableRuleGroup(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Published]),
        );
    }

    public function testAddTarget(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('loadRuleTargets')
            ->willReturn([]);

        $this->layoutResolverHandler
            ->method('addTarget')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $targetCreateStruct = new TargetCreateStruct();
        $targetCreateStruct->value = 'route_name';
        $targetCreateStruct->type = 'route';

        $this->layoutResolverService->addTarget(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]),
            $targetCreateStruct,
        );
    }

    public function testUpdateTarget(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadTarget')
            ->willReturn(new PersistenceTarget());

        $this->layoutResolverHandler
            ->method('updateTarget')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $targetUpdateStruct = new TargetUpdateStruct();
        $targetUpdateStruct->value = 'route_name';

        $this->layoutResolverService->updateTarget(
            Target::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft, 'targetType' => new Route()]),
            $targetUpdateStruct,
        );
    }

    public function testDeleteTarget(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadTarget')
            ->willReturn(new PersistenceTarget());

        $this->layoutResolverHandler
            ->method('deleteTarget')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteTarget(Target::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]));
    }

    public function testAddRuleCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRule')
            ->willReturn(PersistenceRule::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('addRuleCondition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->value = ['parameter_name' => 'param', 'parameter_values' => ['value']];
        $conditionCreateStruct->type = 'route_parameter';

        $this->layoutResolverService->addRuleCondition(
            Rule::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]),
            $conditionCreateStruct,
        );
    }

    public function testAddRuleGroupCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroup')
            ->willReturn(PersistenceRuleGroup::fromArray(['id' => 42]));

        $this->layoutResolverHandler
            ->method('addRuleGroupCondition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->value = ['parameter_name' => 'param', 'parameter_values' => ['value']];
        $conditionCreateStruct->type = 'route_parameter';

        $this->layoutResolverService->addRuleGroupCondition(
            RuleGroup::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]),
            $conditionCreateStruct,
        );
    }

    public function testUpdateRuleCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleCondition')
            ->willReturn(new PersistenceRuleCondition());

        $this->layoutResolverHandler
            ->method('updateCondition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $conditionUpdateStruct = new ConditionUpdateStruct();
        $conditionUpdateStruct->value = ['parameter_name' => 'param', 'parameter_values' => ['value']];

        $this->layoutResolverService->updateRuleCondition(
            RuleCondition::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft, 'conditionType' => new RouteParameter()]),
            $conditionUpdateStruct,
        );
    }

    public function testUpdateRuleGroupCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleGroupCondition')
            ->willReturn(new PersistenceRuleGroupCondition());

        $this->layoutResolverHandler
            ->method('updateCondition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $conditionUpdateStruct = new ConditionUpdateStruct();
        $conditionUpdateStruct->value = ['parameter_name' => 'param', 'parameter_values' => ['value']];

        $this->layoutResolverService->updateRuleGroupCondition(
            RuleGroupCondition::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft, 'conditionType' => new RouteParameter()]),
            $conditionUpdateStruct,
        );
    }

    public function testDeleteCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->method('loadRuleCondition')
            ->willReturn(new PersistenceRuleCondition());

        $this->layoutResolverHandler
            ->method('deleteCondition')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteCondition(RuleCondition::fromArray(['id' => Uuid::uuid4(), 'status' => Status::Draft]));
    }
}
