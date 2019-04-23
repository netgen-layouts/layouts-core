<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Handler;

use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class LayoutResolverHandlerTest extends TestCase
{
    use TestCaseTrait;
    use ExportObjectTrait;

    /**
     * @var \Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    public function setUp(): void
    {
        $this->createDatabase();

        $this->handler = $this->createLayoutResolverHandler();
        $this->layoutHandler = $this->createLayoutHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     */
    public function testLoadRule(): void
    {
        $rule = $this->handler->loadRule(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 1,
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'enabled' => true,
                'priority' => 9,
                'comment' => 'My comment',
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($rule)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     */
    public function testLoadRuleThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "999999"');

        $this->handler->loadRule(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRules
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesData
     */
    public function testLoadRules(): void
    {
        $rules = $this->handler->loadRules(Value::STATUS_PUBLISHED);

        self::assertCount(12, $rules);
        self::assertContainsOnlyInstancesOf(Rule::class, $rules);

        $previousPriority = null;
        foreach ($rules as $index => $rule) {
            if ($index > 0) {
                self::assertLessThanOrEqual($previousPriority, $rule->priority);
            }

            $previousPriority = $rule->priority;
        }
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRules
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesData
     */
    public function testLoadRulesWithLayout(): void
    {
        $rules = $this->handler->loadRules(
            Value::STATUS_PUBLISHED,
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );

        self::assertCount(2, $rules);
        self::assertContainsOnlyInstancesOf(Rule::class, $rules);

        $previousPriority = null;
        foreach ($rules as $index => $rule) {
            if ($index > 0) {
                self::assertLessThanOrEqual($previousPriority, $rule->priority);
            }

            $previousPriority = $rule->priority;
        }
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleCount
     */
    public function testGetRuleCount(): void
    {
        $rules = $this->handler->getRuleCount();

        self::assertSame(12, $rules);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleCount
     */
    public function testGetRuleCountWithLayout(): void
    {
        $rules = $this->handler->getRuleCount(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );

        self::assertSame(2, $rules);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getTargetSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     */
    public function testLoadTarget(): void
    {
        $target = $this->handler->loadTarget(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 1,
                'type' => 'route',
                'value' => 'my_cool_route',
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($target)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     */
    public function testLoadTargetThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "999999"');

        $this->handler->loadTarget(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleTargets
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testLoadRuleTargets(): void
    {
        $targets = $this->handler->loadRuleTargets(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED)
        );

        self::assertNotEmpty($targets);
        self::assertContainsOnlyInstancesOf(Target::class, $targets);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getTargetCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getTargetCount
     */
    public function testGetTargetCount(): void
    {
        $targets = $this->handler->getTargetCount(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED)
        );

        self::assertSame(2, $targets);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getConditionSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     */
    public function testLoadCondition(): void
    {
        $condition = $this->handler->loadCondition(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 2,
                'type' => 'route_parameter',
                'value' => [
                    'parameter_name' => 'some_param',
                    'parameter_values' => [1, 2],
                ],
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($condition)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     */
    public function testLoadConditionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "999999"');

        $this->handler->loadCondition(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     */
    public function testLoadRuleConditions(): void
    {
        $conditions = $this->handler->loadRuleConditions(
            $this->handler->loadRule(2, Value::STATUS_PUBLISHED)
        );

        self::assertNotEmpty($conditions);
        self::assertContainsOnlyInstancesOf(Condition::class, $conditions);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleExists(): void
    {
        self::assertTrue($this->handler->ruleExists(1, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleNotExists(): void
    {
        self::assertFalse($this->handler->ruleExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleNotExistsInStatus(): void
    {
        self::assertFalse($this->handler->ruleExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getRulePriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestRulePriority
     */
    public function testCreateRule(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->layoutId = 3;
        $ruleCreateStruct->priority = 5;
        $ruleCreateStruct->enabled = true;
        $ruleCreateStruct->comment = 'My rule';
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $createdRule = $this->handler->createRule($ruleCreateStruct);

        self::assertSame(13, $createdRule->id);
        self::assertSame(3, $createdRule->layoutId);
        self::assertSame(5, $createdRule->priority);
        self::assertTrue($createdRule->enabled);
        self::assertSame('My rule', $createdRule->comment);
        self::assertSame(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getRulePriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestRulePriority
     */
    public function testCreateRuleWithNoPriority(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $createdRule = $this->handler->createRule($ruleCreateStruct);

        self::assertSame(13, $createdRule->id);
        self::assertNull($createdRule->layoutId);
        self::assertSame(-12, $createdRule->priority);
        self::assertFalse($createdRule->enabled);
        self::assertSame('', $createdRule->comment);
        self::assertSame(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getRulePriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestRulePriority
     */
    public function testCreateRuleWithNoPriorityAndNoRules(): void
    {
        // First delete all rules
        $rules = $this->handler->loadRules(Value::STATUS_PUBLISHED);
        foreach ($rules as $rule) {
            $this->handler->deleteRule($rule->id);
        }

        $rules = $this->handler->loadRules(Value::STATUS_DRAFT);
        foreach ($rules as $rule) {
            $this->handler->deleteRule($rule->id);
        }

        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $createdRule = $this->handler->createRule($ruleCreateStruct);

        self::assertSame(0, $createdRule->priority);
        self::assertSame(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRule(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = '7900306c-0351-5f0a-9b33-5d4f5a1f3943';
        $ruleUpdateStruct->comment = 'New comment';

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        self::assertSame(3, $updatedRule->id);
        self::assertSame(6, $updatedRule->layoutId);
        self::assertSame('7900306c-0351-5f0a-9b33-5d4f5a1f3943', $updatedRule->layoutUuid);
        self::assertSame('New comment', $updatedRule->comment);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRuleWithRemovalOfLinkedLayout(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = false;

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        self::assertSame(3, $updatedRule->id);
        self::assertNull($updatedRule->layoutId);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRuleWithDefaultValues(): void
    {
        $rule = $this->handler->loadRule(3, Value::STATUS_PUBLISHED);
        $ruleUpdateStruct = new RuleUpdateStruct();

        $updatedRule = $this->handler->updateRule($rule, $ruleUpdateStruct);

        self::assertSame(3, $updatedRule->id);
        self::assertSame(3, $updatedRule->layoutId);
        self::assertSame($rule->comment, $updatedRule->comment);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleMetadata
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleData
     */
    public function testUpdateRuleMetadata(): void
    {
        $updatedRule = $this->handler->updateRuleMetadata(
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED),
            RuleMetadataUpdateStruct::fromArray(
                [
                    'enabled' => false,
                    'priority' => 50,
                ]
            )
        );

        self::assertSame(50, $updatedRule->priority);
        self::assertFalse($updatedRule->enabled);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleMetadata
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleData
     */
    public function testUpdateRuleMetadataWithDefaultValues(): void
    {
        $updatedRule = $this->handler->updateRuleMetadata(
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED),
            new RuleMetadataUpdateStruct()
        );

        self::assertSame(5, $updatedRule->priority);
        self::assertTrue($updatedRule->enabled);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::copyRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testCopyRule(): void
    {
        $rule = $this->handler->loadRule(5, Value::STATUS_PUBLISHED);
        $copiedRule = $this->handler->copyRule($rule);

        self::assertSame(13, $copiedRule->id);
        self::assertSame($rule->layoutId, $copiedRule->layoutId);
        self::assertSame($rule->priority, $copiedRule->priority);
        self::assertSame($rule->enabled, $copiedRule->enabled);
        self::assertSame($rule->comment, $copiedRule->comment);
        self::assertSame($rule->status, $copiedRule->status);

        self::assertSame(
            [
                [
                    'id' => 21,
                    'ruleId' => $copiedRule->id,
                    'type' => 'route_prefix',
                    'value' => 'my_second_cool_',
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 22,
                    'ruleId' => $copiedRule->id,
                    'type' => 'route_prefix',
                    'value' => 'my_third_cool_',
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleTargets($copiedRule)
            )
        );

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleId' => $copiedRule->id,
                    'type' => 'condition1',
                    'value' => ['some_value'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleConditions($copiedRule)
            )
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleStatus
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testCreateRuleStatus(): void
    {
        $rule = $this->handler->loadRule(3, Value::STATUS_PUBLISHED);
        $copiedRule = $this->handler->createRuleStatus($rule, Value::STATUS_ARCHIVED);

        self::assertSame($rule->id, $copiedRule->id);
        self::assertSame($rule->layoutId, $copiedRule->layoutId);
        self::assertSame($rule->priority, $copiedRule->priority);
        self::assertSame($rule->enabled, $copiedRule->enabled);
        self::assertSame($rule->comment, $copiedRule->comment);
        self::assertSame(Value::STATUS_ARCHIVED, $copiedRule->status);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleId' => 3,
                    'type' => 'route',
                    'value' => 'my_fourth_cool_route',
                    'status' => Value::STATUS_ARCHIVED,
                ],
                [
                    'id' => 6,
                    'ruleId' => 3,
                    'type' => 'route',
                    'value' => 'my_fifth_cool_route',
                    'status' => Value::STATUS_ARCHIVED,
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleTargets($copiedRule)
            )
        );

        self::assertSame(
            [
                [
                    'id' => 2,
                    'ruleId' => 3,
                    'type' => 'route_parameter',
                    'value' => [
                        'parameter_name' => 'some_param',
                        'parameter_values' => [3, 4],
                    ],
                    'status' => Value::STATUS_ARCHIVED,
                ],
                [
                    'id' => 3,
                    'ruleId' => 3,
                    'type' => 'route_parameter',
                    'value' => [
                        'parameter_name' => 'some_other_param',
                        'parameter_values' => [5, 6],
                    ],
                    'status' => Value::STATUS_ARCHIVED,
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleConditions($copiedRule)
            )
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleTargets
     */
    public function testDeleteRule(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "3"');

        $this->handler->deleteRule(3);

        $this->handler->loadRule(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleTargets
     */
    public function testDeleteRuleInOneStatus(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "5"');

        $this->handler->deleteRule(5, Value::STATUS_DRAFT);

        // First, verify that NOT all rule statuses are deleted
        try {
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the rule in draft status deleted other/all statuses.');
        }

        $this->handler->loadRule(5, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::addTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     */
    public function testAddTarget(): void
    {
        $targetCreateStruct = new TargetCreateStruct();
        $targetCreateStruct->type = 'target';
        $targetCreateStruct->value = '42';

        $target = $this->handler->addTarget(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED),
            $targetCreateStruct
        );

        self::assertSame(
            [
                'id' => 21,
                'ruleId' => 1,
                'type' => 'target',
                'value' => '42',
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($target)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateTarget
     */
    public function testUpdateTarget(): void
    {
        $targetUpdateStruct = new TargetUpdateStruct();
        $targetUpdateStruct->value = 'my_new_route';

        $target = $this->handler->updateTarget(
            $this->handler->loadTarget(1, Value::STATUS_PUBLISHED),
            $targetUpdateStruct
        );

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 1,
                'type' => 'route',
                'value' => 'my_new_route',
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($target)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteTarget
     */
    public function testDeleteTarget(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "2"');

        $target = $this->handler->loadTarget(2, Value::STATUS_PUBLISHED);

        $this->handler->deleteTarget($target);

        $this->handler->loadTarget(2, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     */
    public function testAddCondition(): void
    {
        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->type = 'condition';
        $conditionCreateStruct->value = ['param' => 'value'];

        $condition = $this->handler->addCondition(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $conditionCreateStruct
        );

        self::assertSame(
            [
                'id' => 5,
                'ruleId' => 3,
                'type' => 'condition',
                'value' => ['param' => 'value'],
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($condition)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateCondition
     */
    public function testUpdateCondition(): void
    {
        $conditionUpdateStruct = new ConditionUpdateStruct();
        $conditionUpdateStruct->value = ['new_param' => 'new_value'];

        $condition = $this->handler->updateCondition(
            $this->handler->loadCondition(1, Value::STATUS_PUBLISHED),
            $conditionUpdateStruct
        );

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 2,
                'type' => 'route_parameter',
                'value' => ['new_param' => 'new_value'],
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($condition)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteCondition
     */
    public function testDeleteCondition(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "2"');

        $this->handler->deleteCondition(
            $this->handler->loadCondition(2, Value::STATUS_PUBLISHED)
        );

        $this->handler->loadCondition(2, Value::STATUS_PUBLISHED);
    }
}
