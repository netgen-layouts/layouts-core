<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter;
use Netgen\BlockManager\Layout\Resolver\TargetType\Route;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;

final class LayoutResolverServiceTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createRule
     */
    public function testCreateRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('createRule')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createRule(new RuleCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateRule')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRule(
            Rule::fromArray(['status' => Value::STATUS_DRAFT]),
            new RuleUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRuleMetadata
     */
    public function testUpdateRuleMetadata(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateRuleMetadata')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRuleMetadata(
            Rule::fromArray(['status' => Value::STATUS_PUBLISHED]),
            new RuleMetadataUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('copyRule')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->copyRule(Rule::fromArray(['status' => Rule::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('ruleExists')
            ->will(self::returnValue(false));

        $this->layoutResolverHandler
            ->expects(self::at(2))
            ->method('deleteRule')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createDraft(Rule::fromArray(['status' => Value::STATUS_PUBLISHED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::discardDraft
     */
    public function testDiscardDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteRule')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->discardDraft(Rule::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteRule')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->publishRule(Rule::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::restoreFromArchive
     */
    public function testRestoreFromArchive(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(2))
            ->method('deleteRule')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->restoreFromArchive(Rule::fromArray(['status' => Rule::STATUS_ARCHIVED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteRule
     */
    public function testDeleteRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteRule')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteRule(Rule::fromArray(['status' => Rule::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     */
    public function testEnableRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(
                self::returnValue(
                    PersistenceRule::fromArray(
                        [
                            'layoutId' => 42,
                            'enabled' => false,
                        ]
                    )
                )
            );

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('getTargetCount')
            ->will(self::returnValue(2));

        $this->layoutResolverHandler
            ->expects(self::at(2))
            ->method('updateRuleMetadata')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->enableRule(Rule::fromArray(['status' => Value::STATUS_PUBLISHED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     */
    public function testDisableRule(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(
                self::returnValue(
                    PersistenceRule::fromArray(
                        [
                            'enabled' => true,
                        ]
                    )
                )
            );

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateRuleMetadata')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->disableRule(Rule::fromArray(['status' => Value::STATUS_PUBLISHED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     */
    public function testAddTarget(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('loadRuleTargets')
            ->will(self::returnValue([]));

        $this->layoutResolverHandler
            ->expects(self::at(2))
            ->method('addTarget')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $targetCreateStruct = new TargetCreateStruct();
        $targetCreateStruct->type = 'route';

        $this->layoutResolverService->addTarget(
            Rule::fromArray(['status' => Value::STATUS_DRAFT]),
            $targetCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateTarget
     */
    public function testUpdateTarget(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadTarget')
            ->will(self::returnValue(new PersistenceTarget()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateTarget')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateTarget(
            Target::fromArray(['status' => Value::STATUS_DRAFT, 'targetType' => new Route()]),
            new TargetUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteTarget
     */
    public function testDeleteTarget(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadTarget')
            ->will(self::returnValue(new PersistenceTarget()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteTarget')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteTarget(Target::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addCondition
     */
    public function testAddCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadRule')
            ->will(self::returnValue(new PersistenceRule()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('addCondition')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->type = 'route_parameter';

        $this->layoutResolverService->addCondition(
            Rule::fromArray(['status' => Value::STATUS_DRAFT]),
            $conditionCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateCondition
     */
    public function testUpdateCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadCondition')
            ->will(self::returnValue(new PersistenceCondition()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('updateCondition')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateCondition(
            Condition::fromArray(['status' => Value::STATUS_DRAFT, 'conditionType' => new RouteParameter()]),
            new ConditionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteCondition
     */
    public function testDeleteCondition(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutResolverHandler
            ->expects(self::at(0))
            ->method('loadCondition')
            ->will(self::returnValue(new PersistenceCondition()));

        $this->layoutResolverHandler
            ->expects(self::at(1))
            ->method('deleteCondition')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteCondition(Condition::fromArray(['status' => Value::STATUS_DRAFT]));
    }
}
