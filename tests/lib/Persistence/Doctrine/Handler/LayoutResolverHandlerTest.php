<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Handler;

use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\UuidGeneratorTrait;
use PHPUnit\Framework\TestCase;

final class LayoutResolverHandlerTest extends TestCase
{
    use ExportObjectTrait;
    use TestCaseTrait;
    use UuidGeneratorTrait;

    private LayoutResolverHandlerInterface $handler;

    private LayoutHandlerInterface $layoutHandler;

    protected function setUp(): void
    {
        $this->createDatabase();

        $this->handler = $this->createLayoutResolverHandler();
        $this->layoutHandler = $this->createLayoutHandler();
    }

    /**
     * Tears down the tests.
     */
    protected function tearDown(): void
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
                'description' => 'My description',
                'enabled' => true,
                'id' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'priority' => 9,
                'ruleGroupId' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => '26768324-03dd-5952-8a55-4b449d6cd634',
            ],
            $this->exportObject($rule),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     */
    public function testLoadRuleThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "999"');

        $this->handler->loadRule(999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleGroupSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleGroupUuid
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupData
     */
    public function testLoadRuleGroup(): void
    {
        $rule = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'depth' => 1,
                'description' => 'My description',
                'enabled' => true,
                'id' => 2,
                'name' => 'First group',
                'parentId' => 1,
                'parentUuid' => RuleGroup::ROOT_UUID,
                'path' => '/1/2/',
                'priority' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => 'b4f85f38-de3f-4af7-9a5f-21df63a49da9',
            ],
            $this->exportObject($rule),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleGroupUuid
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupData
     */
    public function testLoadRuleGroupThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "999"');

        $this->handler->loadRuleGroup(999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRulesForLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesForLayoutData
     */
    public function testLoadRulesForLayout(): void
    {
        $rules = $this->handler->loadRulesForLayout(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED),
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleCountForLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleCountForLayout
     */
    public function testGetRuleCountForLayout(): void
    {
        $rules = $this->handler->getRuleCountForLayout(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED),
        );

        self::assertSame(2, $rules);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRulesFromGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesFromGroupData
     */
    public function testLoadRulesFromGroup(): void
    {
        $rules = $this->handler->loadRulesFromGroup(
            $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED),
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleCountFromGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleCountFromGroup
     */
    public function testGetRuleCountFromGroup(): void
    {
        $rules = $this->handler->getRuleCountFromGroup(
            $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED),
        );

        self::assertSame(2, $rules);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleGroups
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupsData
     */
    public function testLoadRuleGroups(): void
    {
        $ruleGroups = $this->handler->loadRuleGroups(
            $this->handler->loadRuleGroup(1, Value::STATUS_PUBLISHED),
        );

        self::assertCount(2, $ruleGroups);
        self::assertContainsOnlyInstancesOf(RuleGroup::class, $ruleGroups);

        $previousPriority = null;
        foreach ($ruleGroups as $index => $ruleGroup) {
            if ($index > 0) {
                self::assertLessThanOrEqual($previousPriority, $ruleGroup->priority);
            }

            $previousPriority = $ruleGroup->priority;
        }
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleGroupCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleGroupCount
     */
    public function testGetRuleGroupCount(): void
    {
        $ruleGroups = $this->handler->getRuleGroupCount(
            $this->handler->loadRuleGroup(1, Value::STATUS_PUBLISHED),
        );

        self::assertSame(2, $ruleGroups);
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
                'ruleUuid' => '26768324-03dd-5952-8a55-4b449d6cd634',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'route',
                'uuid' => 'c7c5cdca-02da-5ba5-ad9e-d25cbc4b1b46',
                'value' => 'my_cool_route',
            ],
            $this->exportObject($target),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     */
    public function testLoadTargetThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "999"');

        $this->handler->loadTarget(999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleTargets
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testLoadRuleTargets(): void
    {
        $targets = $this->handler->loadRuleTargets(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED),
        );

        self::assertNotEmpty($targets);
        self::assertContainsOnlyInstancesOf(Target::class, $targets);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleTargetCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleTargetCount
     */
    public function testGetRuleTargetCount(): void
    {
        $targets = $this->handler->getRuleTargetCount(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED),
        );

        self::assertSame(2, $targets);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleConditionSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionData
     */
    public function testLoadRuleCondition(): void
    {
        $condition = $this->handler->loadRuleCondition(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 2,
                'ruleUuid' => '55622437-f700-5378-99c9-7dafe89a8fb6',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'route_parameter',
                'uuid' => '35f4594c-6674-5815-add6-07f288b79686',
                'value' => [
                    'parameter_name' => 'some_param',
                    'parameter_values' => [1, 2],
                ],
            ],
            $this->exportObject($condition),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionData
     */
    public function testLoadRuleConditionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "999"');

        $this->handler->loadRuleCondition(999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleGroupCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleGroupConditionSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionData
     */
    public function testLoadRuleGroupCondition(): void
    {
        $condition = $this->handler->loadRuleGroupCondition(5, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 5,
                'ruleGroupId' => 2,
                'ruleGroupUuid' => 'b4f85f38-de3f-4af7-9a5f-21df63a49da9',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'condition1',
                'uuid' => 'b084d390-01ea-464b-8282-797b6ef9ef1e',
                'value' => [
                    'some_other_value',
                ],
            ],
            $this->exportObject($condition),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleGroupCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionData
     */
    public function testLoadRuleGroupConditionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "999"');

        $this->handler->loadRuleGroupCondition(999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     */
    public function testLoadRuleConditions(): void
    {
        $conditions = $this->handler->loadRuleConditions(
            $this->handler->loadRule(2, Value::STATUS_PUBLISHED),
        );

        self::assertNotEmpty($conditions);
        self::assertContainsOnlyInstancesOf(RuleCondition::class, $conditions);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleGroupConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionsData
     */
    public function testLoadRuleGroupConditions(): void
    {
        $conditions = $this->handler->loadRuleGroupConditions(
            $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED),
        );

        self::assertNotEmpty($conditions);
        self::assertContainsOnlyInstancesOf(RuleGroupCondition::class, $conditions);
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
        self::assertFalse($this->handler->ruleExists(999, Value::STATUS_PUBLISHED));
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRule(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = null;
        $ruleCreateStruct->layoutId = 'd8e55af7-cf62-5f28-ae15-331b457d82e9';
        $ruleCreateStruct->priority = 5;
        $ruleCreateStruct->enabled = true;
        $ruleCreateStruct->description = 'My rule';
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $ruleGroup = $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED);

        $createdRule = $this->withUuids(
            fn (): Rule => $this->handler->createRule($ruleCreateStruct, $ruleGroup),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(12, $createdRule->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRule->uuid);
        self::assertSame(1, $createdRule->ruleGroupId);
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $createdRule->layoutUuid);
        self::assertSame(5, $createdRule->priority);
        self::assertTrue($createdRule->enabled);
        self::assertSame('My rule', $createdRule->description);
        self::assertSame(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRuleWithCustomUuid(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = '0f714915-eef0-4dc1-b22b-1107cb1ab92b';
        $ruleCreateStruct->layoutId = 'd8e55af7-cf62-5f28-ae15-331b457d82e9';
        $ruleCreateStruct->priority = 5;
        $ruleCreateStruct->enabled = true;
        $ruleCreateStruct->description = 'My rule';
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $ruleGroup = $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED);

        $createdRule = $this->handler->createRule($ruleCreateStruct, $ruleGroup);

        self::assertSame(12, $createdRule->id);
        self::assertSame('0f714915-eef0-4dc1-b22b-1107cb1ab92b', $createdRule->uuid);
        self::assertSame(1, $createdRule->ruleGroupId);
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $createdRule->layoutUuid);
        self::assertSame(5, $createdRule->priority);
        self::assertTrue($createdRule->enabled);
        self::assertSame('My rule', $createdRule->description);
        self::assertSame(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRuleWithExistingUuidThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "uuid" has an invalid state. Rule with provided UUID already exists.');

        $ruleGroup = $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED);

        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = '26768324-03dd-5952-8a55-4b449d6cd634';
        $ruleCreateStruct->layoutId = 'd8e55af7-cf62-5f28-ae15-331b457d82e9';
        $ruleCreateStruct->priority = 5;
        $ruleCreateStruct->enabled = true;
        $ruleCreateStruct->description = 'My rule';
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $this->handler->createRule($ruleCreateStruct, $ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRuleWithNoPriority(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = null;
        $ruleCreateStruct->layoutId = null;
        $ruleCreateStruct->priority = null;
        $ruleCreateStruct->enabled = false;
        $ruleCreateStruct->description = '';
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $ruleGroup = $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED);

        $createdRule = $this->withUuids(
            fn (): Rule => $this->handler->createRule($ruleCreateStruct, $ruleGroup),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(12, $createdRule->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRule->uuid);
        self::assertSame(1, $createdRule->ruleGroupId);
        self::assertNull($createdRule->layoutUuid);
        self::assertSame(-11, $createdRule->priority);
        self::assertFalse($createdRule->enabled);
        self::assertSame('', $createdRule->description);
        self::assertSame(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRuleWithNoPriorityAndNoRules(): void
    {
        // First delete all rules
        $rules = $this->handler->loadRulesFromGroup(
            $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED),
        );

        foreach ($rules as $rule) {
            $this->handler->deleteRule($rule->id);
        }

        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = null;
        $ruleCreateStruct->layoutId = null;
        $ruleCreateStruct->priority = null;
        $ruleCreateStruct->enabled = false;
        $ruleCreateStruct->description = '';
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $ruleGroup = $this->handler->loadRuleGroup('eb6311eb-24f6-4143-b476-99979a885a7e', Value::STATUS_PUBLISHED);

        $createdRule = $this->handler->createRule($ruleCreateStruct, $ruleGroup);

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
        $ruleUpdateStruct->description = 'New description';

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct,
        );

        self::assertSame(3, $updatedRule->id);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $updatedRule->uuid);
        self::assertSame(2, $updatedRule->ruleGroupId);
        self::assertSame('7900306c-0351-5f0a-9b33-5d4f5a1f3943', $updatedRule->layoutUuid);
        self::assertSame('New description', $updatedRule->description);
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
            $ruleUpdateStruct,
        );

        self::assertSame(3, $updatedRule->id);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $updatedRule->uuid);
        self::assertSame(2, $updatedRule->ruleGroupId);
        self::assertNull($updatedRule->layoutUuid);
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
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $updatedRule->uuid);
        self::assertSame($rule->ruleGroupId, $updatedRule->ruleGroupId);
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $updatedRule->layoutUuid);
        self::assertSame($rule->description, $updatedRule->description);
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
                ],
            ),
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
            new RuleMetadataUpdateStruct(),
        );

        self::assertSame(5, $updatedRule->priority);
        self::assertTrue($updatedRule->enabled);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::copyRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addRuleCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testCopyRule(): void
    {
        $rule = $this->handler->loadRule(5, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(1, Value::STATUS_PUBLISHED);

        $copiedRule = $this->withUuids(
            fn (): Rule => $this->handler->copyRule($rule, $targetGroup),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'efd1d54a-5d53-518f-91a5-f4965c242a67',
                '1169074c-8779-5b64-afec-c910705e418a',
                'aaa3659b-b574-5e6b-8902-0ea37f576469',
            ],
        );

        self::assertSame(12, $copiedRule->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedRule->uuid);
        self::assertSame($targetGroup->id, $copiedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $copiedRule->layoutUuid);
        self::assertSame($rule->priority, $copiedRule->priority);
        self::assertSame($rule->enabled, $copiedRule->enabled);
        self::assertSame($rule->description, $copiedRule->description);
        self::assertSame($rule->status, $copiedRule->status);

        self::assertSame(
            [
                [
                    'id' => 21,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'route_prefix',
                    'uuid' => 'efd1d54a-5d53-518f-91a5-f4965c242a67',
                    'value' => 'my_second_cool_',
                ],
                [
                    'id' => 22,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'route_prefix',
                    'uuid' => '1169074c-8779-5b64-afec-c910705e418a',
                    'value' => 'my_third_cool_',
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleTargets($copiedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 7,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => 'aaa3659b-b574-5e6b-8902-0ea37f576469',
                    'value' => ['some_value'],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleConditions($copiedRule),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::copyRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addRuleCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testCopyRuleToOtherGroup(): void
    {
        $rule = $this->handler->loadRule(5, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);

        $copiedRule = $this->withUuids(
            fn (): Rule => $this->handler->copyRule($rule, $targetGroup),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'efd1d54a-5d53-518f-91a5-f4965c242a67',
                '1169074c-8779-5b64-afec-c910705e418a',
                'aaa3659b-b574-5e6b-8902-0ea37f576469',
            ],
        );

        self::assertSame(12, $copiedRule->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedRule->uuid);
        self::assertSame($targetGroup->id, $copiedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $copiedRule->layoutUuid);
        self::assertSame($rule->priority, $copiedRule->priority);
        self::assertSame($rule->enabled, $copiedRule->enabled);
        self::assertSame($rule->description, $copiedRule->description);
        self::assertSame($rule->status, $copiedRule->status);

        self::assertSame(
            [
                [
                    'id' => 21,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'route_prefix',
                    'uuid' => 'efd1d54a-5d53-518f-91a5-f4965c242a67',
                    'value' => 'my_second_cool_',
                ],
                [
                    'id' => 22,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'route_prefix',
                    'uuid' => '1169074c-8779-5b64-afec-c910705e418a',
                    'value' => 'my_third_cool_',
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleTargets($copiedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 7,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => 'aaa3659b-b574-5e6b-8902-0ea37f576469',
                    'value' => ['some_value'],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleConditions($copiedRule),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::moveRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::moveRule
     */
    public function testMoveRule(): void
    {
        $rule = $this->handler->loadRule(5, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(4, Value::STATUS_PUBLISHED);

        $movedRule = $this->handler->moveRule($rule, $targetGroup);

        self::assertSame($rule->id, $movedRule->id);
        self::assertSame($rule->uuid, $movedRule->uuid);
        self::assertSame($targetGroup->id, $movedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $movedRule->layoutUuid);
        self::assertSame($rule->priority, $movedRule->priority);
        self::assertSame($rule->enabled, $movedRule->enabled);
        self::assertSame($rule->description, $movedRule->description);
        self::assertSame($rule->status, $movedRule->status);

        self::assertSame(
            [
                [
                    'id' => 9,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'route_prefix',
                    'uuid' => '5104e4e7-1a20-5db8-8857-5ab99f1290b9',
                    'value' => 'my_second_cool_',
                ],
                [
                    'id' => 10,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'route_prefix',
                    'uuid' => 'f0019d3e-5868-503d-b81b-5263af428495',
                    'value' => 'my_third_cool_',
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleTargets($movedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 4,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => '7db46c94-3139-5a3d-9b2a-b2d28e7573ca',
                    'value' => ['some_value'],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleConditions($movedRule),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::moveRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::moveRule
     */
    public function testMoveRuleWithSpecifiedPriority(): void
    {
        $rule = $this->handler->loadRule(5, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(4, Value::STATUS_PUBLISHED);

        $movedRule = $this->handler->moveRule($rule, $targetGroup, 42);

        self::assertSame($rule->id, $movedRule->id);
        self::assertSame($rule->uuid, $movedRule->uuid);
        self::assertSame($targetGroup->id, $movedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $movedRule->layoutUuid);
        self::assertSame(42, $movedRule->priority);
        self::assertSame($rule->enabled, $movedRule->enabled);
        self::assertSame($rule->description, $movedRule->description);
        self::assertSame($rule->status, $movedRule->status);

        self::assertSame(
            [
                [
                    'id' => 9,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'route_prefix',
                    'uuid' => '5104e4e7-1a20-5db8-8857-5ab99f1290b9',
                    'value' => 'my_second_cool_',
                ],
                [
                    'id' => 10,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'route_prefix',
                    'uuid' => 'f0019d3e-5868-503d-b81b-5263af428495',
                    'value' => 'my_third_cool_',
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleTargets($movedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 4,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => '7db46c94-3139-5a3d-9b2a-b2d28e7573ca',
                    'value' => ['some_value'],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleConditions($movedRule),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::moveRule
     */
    public function testMoveRuleToSameGroupThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Rule is already in specified target group.');

        $rule = $this->handler->loadRule(5, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(1, Value::STATUS_PUBLISHED);

        $this->handler->moveRule($rule, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleStatus
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addRuleCondition
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
        self::assertSame($rule->uuid, $copiedRule->uuid);
        self::assertSame($rule->ruleGroupId, $copiedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $copiedRule->layoutUuid);
        self::assertSame($rule->priority, $copiedRule->priority);
        self::assertSame($rule->enabled, $copiedRule->enabled);
        self::assertSame($rule->description, $copiedRule->description);
        self::assertSame(Value::STATUS_ARCHIVED, $copiedRule->status);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleId' => 3,
                    'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                    'status' => Value::STATUS_ARCHIVED,
                    'type' => 'route',
                    'uuid' => '445e885e-1ad5-584b-b51b-263fb66805c2',
                    'value' => 'my_fourth_cool_route',
                ],
                [
                    'id' => 6,
                    'ruleId' => 3,
                    'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                    'status' => Value::STATUS_ARCHIVED,
                    'type' => 'route',
                    'uuid' => 'feee231e-4fee-514a-a938-a2769036c07b',
                    'value' => 'my_fifth_cool_route',
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleTargets($copiedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 2,
                    'ruleId' => 3,
                    'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                    'status' => Value::STATUS_ARCHIVED,
                    'type' => 'route_parameter',
                    'uuid' => '9a6c8459-5fda-5d4b-b06e-06f637ab6e01',
                    'value' => [
                        'parameter_name' => 'some_param',
                        'parameter_values' => [3, 4],
                    ],
                ],
                [
                    'id' => 3,
                    'ruleId' => 3,
                    'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                    'status' => Value::STATUS_ARCHIVED,
                    'type' => 'route_parameter',
                    'uuid' => 'dd49afcd-aab0-5970-b7b8-413238faf539',
                    'value' => [
                        'parameter_name' => 'some_other_param',
                        'parameter_values' => [5, 6],
                    ],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleConditions($copiedRule),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRule
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleTargets
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionIds
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionIds
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleGroupExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleGroupExists
     */
    public function testRuleGroupExists(): void
    {
        self::assertTrue($this->handler->ruleGroupExists(1, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleGroupExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleGroupExists
     */
    public function testRuleGroupNotExists(): void
    {
        self::assertFalse($this->handler->ruleGroupExists(999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleGroupExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleGroupExists
     */
    public function testRuleGroupNotExistsInStatus(): void
    {
        self::assertFalse($this->handler->ruleGroupExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRuleGroup(): void
    {
        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = null;
        $ruleGroupCreateStruct->name = 'New rule group';
        $ruleGroupCreateStruct->description = 'My rule group';
        $ruleGroupCreateStruct->priority = 5;
        $ruleGroupCreateStruct->enabled = true;
        $ruleGroupCreateStruct->status = Value::STATUS_DRAFT;

        $parentGroup = $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED);

        $createdRuleGroup = $this->withUuids(
            fn (): RuleGroup => $this->handler->createRuleGroup($ruleGroupCreateStruct, $parentGroup),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(5, $createdRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRuleGroup->uuid);
        self::assertSame($parentGroup->depth + 1, $createdRuleGroup->depth);
        self::assertSame($parentGroup->path . $createdRuleGroup->id . '/', $createdRuleGroup->path);
        self::assertSame($parentGroup->id, $createdRuleGroup->parentId);
        self::assertSame($parentGroup->uuid, $createdRuleGroup->parentUuid);
        self::assertSame('New rule group', $createdRuleGroup->name);
        self::assertSame('My rule group', $createdRuleGroup->description);
        self::assertSame(5, $createdRuleGroup->priority);
        self::assertTrue($createdRuleGroup->enabled);
        self::assertSame(Value::STATUS_DRAFT, $createdRuleGroup->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRuleGroupWithCustomUuid(): void
    {
        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = 'f06f245a-f951-52c8-bfa3-84c80154eadc';
        $ruleGroupCreateStruct->name = 'New rule group';
        $ruleGroupCreateStruct->description = 'My rule group';
        $ruleGroupCreateStruct->priority = 5;
        $ruleGroupCreateStruct->enabled = true;
        $ruleGroupCreateStruct->status = Value::STATUS_DRAFT;

        $parentGroup = $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED);

        $createdRuleGroup = $this->handler->createRuleGroup($ruleGroupCreateStruct, $parentGroup);

        self::assertSame(5, $createdRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRuleGroup->uuid);
        self::assertSame($parentGroup->depth + 1, $createdRuleGroup->depth);
        self::assertSame($parentGroup->path . $createdRuleGroup->id . '/', $createdRuleGroup->path);
        self::assertSame($parentGroup->id, $createdRuleGroup->parentId);
        self::assertSame($parentGroup->uuid, $createdRuleGroup->parentUuid);
        self::assertSame('New rule group', $createdRuleGroup->name);
        self::assertSame('My rule group', $createdRuleGroup->description);
        self::assertSame(5, $createdRuleGroup->priority);
        self::assertTrue($createdRuleGroup->enabled);
        self::assertSame(Value::STATUS_DRAFT, $createdRuleGroup->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRuleGroupWithNoPriority(): void
    {
        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = null;
        $ruleGroupCreateStruct->name = '';
        $ruleGroupCreateStruct->description = '';
        $ruleGroupCreateStruct->priority = null;
        $ruleGroupCreateStruct->enabled = false;
        $ruleGroupCreateStruct->status = Value::STATUS_DRAFT;

        $parentGroup = $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED);

        $createdRuleGroup = $this->withUuids(
            fn (): RuleGroup => $this->handler->createRuleGroup($ruleGroupCreateStruct, $parentGroup),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(5, $createdRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRuleGroup->uuid);
        self::assertSame($parentGroup->depth + 1, $createdRuleGroup->depth);
        self::assertSame($parentGroup->path . $createdRuleGroup->id . '/', $createdRuleGroup->path);
        self::assertSame($parentGroup->id, $createdRuleGroup->parentId);
        self::assertSame($parentGroup->uuid, $createdRuleGroup->parentUuid);
        self::assertSame('', $createdRuleGroup->name);
        self::assertSame('', $createdRuleGroup->description);
        self::assertSame(-11, $createdRuleGroup->priority);
        self::assertFalse($createdRuleGroup->enabled);
        self::assertSame(Value::STATUS_DRAFT, $createdRuleGroup->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRuleGroupWithNoPriorityAndNoRulesAndRuleGroups(): void
    {
        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = null;
        $ruleGroupCreateStruct->name = '';
        $ruleGroupCreateStruct->description = '';
        $ruleGroupCreateStruct->priority = null;
        $ruleGroupCreateStruct->enabled = false;
        $ruleGroupCreateStruct->status = Value::STATUS_DRAFT;

        $ruleGroup = $this->handler->loadRuleGroup(3, Value::STATUS_PUBLISHED);

        $createdRuleGroup = $this->handler->createRuleGroup($ruleGroupCreateStruct, $ruleGroup);

        self::assertSame(0, $createdRuleGroup->priority);
        self::assertSame(Value::STATUS_DRAFT, $createdRuleGroup->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRootRuleGroupWithExistingRootRuleGroupThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Root rule group already exists.');

        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->name = 'My rule group';
        $ruleGroupCreateStruct->description = 'My rule group';
        $ruleGroupCreateStruct->priority = 5;
        $ruleGroupCreateStruct->enabled = true;
        $ruleGroupCreateStruct->status = Value::STATUS_DRAFT;

        $this->handler->createRuleGroup($ruleGroupCreateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::getPriority
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestPriority
     */
    public function testCreateRuleGroupWithExistingUuidThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "uuid" has an invalid state. Rule group with provided UUID already exists.');

        $ruleGroup = $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED);

        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = 'b4f85f38-de3f-4af7-9a5f-21df63a49da9';
        $ruleGroupCreateStruct->name = 'My rule group';
        $ruleGroupCreateStruct->description = 'My rule group';
        $ruleGroupCreateStruct->priority = 5;
        $ruleGroupCreateStruct->enabled = true;
        $ruleGroupCreateStruct->status = Value::STATUS_DRAFT;

        $this->handler->createRuleGroup($ruleGroupCreateStruct, $ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleGroup
     */
    public function testUpdateRuleGroup(): void
    {
        $ruleGroupUpdateStruct = new RuleGroupUpdateStruct();
        $ruleGroupUpdateStruct->name = 'New name';
        $ruleGroupUpdateStruct->description = 'New description';

        $updatedRuleGroup = $this->handler->updateRuleGroup(
            $this->handler->loadRuleGroup(3, Value::STATUS_PUBLISHED),
            $ruleGroupUpdateStruct,
        );

        self::assertSame(3, $updatedRuleGroup->id);
        self::assertSame('eb6311eb-24f6-4143-b476-99979a885a7e', $updatedRuleGroup->uuid);
        self::assertSame('New name', $updatedRuleGroup->name);
        self::assertSame('New description', $updatedRuleGroup->description);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRuleGroup->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleGroup
     */
    public function testUpdateRuleGroupWithDefaultValues(): void
    {
        $ruleGroup = $this->handler->loadRuleGroup(3, Value::STATUS_PUBLISHED);
        $ruleGroupUpdateStruct = new RuleGroupUpdateStruct();

        $updatedRuleGroup = $this->handler->updateRuleGroup($ruleGroup, $ruleGroupUpdateStruct);

        self::assertSame(3, $updatedRuleGroup->id);
        self::assertSame('eb6311eb-24f6-4143-b476-99979a885a7e', $updatedRuleGroup->uuid);
        self::assertSame($ruleGroup->name, $updatedRuleGroup->name);
        self::assertSame($ruleGroup->description, $updatedRuleGroup->description);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRuleGroup->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleGroupMetadata
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleGroupData
     */
    public function testUpdateRuleGroupMetadata(): void
    {
        $updatedRuleGroup = $this->handler->updateRuleGroupMetadata(
            $this->handler->loadRuleGroup(3, Value::STATUS_PUBLISHED),
            RuleGroupMetadataUpdateStruct::fromArray(
                [
                    'enabled' => false,
                    'priority' => 50,
                ],
            ),
        );

        self::assertSame(50, $updatedRuleGroup->priority);
        self::assertFalse($updatedRuleGroup->enabled);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRuleGroup->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleGroupMetadata
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleGroupData
     */
    public function testUpdateRuleGroupMetadataWithDefaultValues(): void
    {
        $updatedRuleGroup = $this->handler->updateRuleGroupMetadata(
            $this->handler->loadRuleGroup(3, Value::STATUS_PUBLISHED),
            new RuleGroupMetadataUpdateStruct(),
        );

        self::assertSame(2, $updatedRuleGroup->priority);
        self::assertFalse($updatedRuleGroup->enabled);
        self::assertSame(Value::STATUS_PUBLISHED, $updatedRuleGroup->status);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::copyRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addRuleGroupCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleGroupUuid
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionsData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupData
     */
    public function testCopyRuleGroup(): void
    {
        $ruleGroup = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(1, Value::STATUS_PUBLISHED);

        $copiedRuleGroup = $this->withUuids(
            fn (): RuleGroup => $this->handler->copyRuleGroup($ruleGroup, $targetGroup),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'efd1d54a-5d53-518f-91a5-f4965c242a67',
                '1169074c-8779-5b64-afec-c910705e418a',
            ],
        );

        self::assertSame(5, $copiedRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedRuleGroup->uuid);
        self::assertSame($targetGroup->depth + 1, $copiedRuleGroup->depth);
        self::assertSame($targetGroup->path . $copiedRuleGroup->id . '/', $copiedRuleGroup->path);
        self::assertSame($targetGroup->id, $copiedRuleGroup->parentId);
        self::assertSame($targetGroup->uuid, $copiedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $copiedRuleGroup->name);
        self::assertSame($ruleGroup->description, $copiedRuleGroup->description);
        self::assertSame($ruleGroup->priority, $copiedRuleGroup->priority);
        self::assertSame($ruleGroup->enabled, $copiedRuleGroup->enabled);
        self::assertSame($ruleGroup->status, $copiedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 7,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => 'efd1d54a-5d53-518f-91a5-f4965c242a67',
                    'value' => ['some_other_value'],
                ],
                [
                    'id' => 8,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => '1169074c-8779-5b64-afec-c910705e418a',
                    'value' => ['some_third_value'],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleGroupConditions($copiedRuleGroup),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::copyRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addRuleGroupCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleGroupUuid
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionsData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupData
     */
    public function testCopyRuleGroupWithChildren(): void
    {
        $ruleGroup = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(1, Value::STATUS_PUBLISHED);

        $copiedRuleGroup = $this->withUuids(
            fn (): RuleGroup => $this->handler->copyRuleGroup($ruleGroup, $targetGroup, true),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'efd1d54a-5d53-518f-91a5-f4965c242a67',
                '1169074c-8779-5b64-afec-c910705e418a',
                '5848c206-341b-4142-a2e0-b27a5309cc6c',
                'ecae57e9-42ca-423f-80f4-7c4c129fc439',
                '9da71481-a9ec-47f1-800f-b2fc4e0fd136',
                '85286fb0-efe7-4ab2-97a3-e4a130006afd',
                'a2bd079c-b008-41d5-a4fe-179f3bf1c3da',
                '4d8881cb-e9ac-4fd0-a0f2-69303192febd',
                'a802af45-7d1a-4c01-99e2-246f9d41541f',
                '094d64e6-b22f-46ef-b573-63ee2e308530',
                'ce747405-4641-436a-8fe2-7969354e6452',
                'aa82ce80-c4c4-4b80-819a-baf5c8af69d6',
                '81ecb297-52a0-4ec7-89d5-eebe595c8d2c',
                'b1e5bc79-610f-41b7-9505-7567435cb80e',
            ],
        );

        self::assertSame(5, $copiedRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedRuleGroup->uuid);
        self::assertSame($targetGroup->depth + 1, $copiedRuleGroup->depth);
        self::assertSame($targetGroup->path . $copiedRuleGroup->id . '/', $copiedRuleGroup->path);
        self::assertSame($targetGroup->id, $copiedRuleGroup->parentId);
        self::assertSame($targetGroup->uuid, $copiedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $copiedRuleGroup->name);
        self::assertSame($ruleGroup->description, $copiedRuleGroup->description);
        self::assertSame($ruleGroup->priority, $copiedRuleGroup->priority);
        self::assertSame($ruleGroup->enabled, $copiedRuleGroup->enabled);
        self::assertSame($ruleGroup->status, $copiedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 7,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => 'efd1d54a-5d53-518f-91a5-f4965c242a67',
                    'value' => ['some_other_value'],
                ],
                [
                    'id' => 8,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => '1169074c-8779-5b64-afec-c910705e418a',
                    'value' => ['some_third_value'],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleGroupConditions($copiedRuleGroup),
            ),
        );

        self::assertSame(1, $this->handler->getRuleGroupCount($copiedRuleGroup));

        $copiedSubGroup = $this->handler->loadRuleGroup('ce747405-4641-436a-8fe2-7969354e6452', Value::STATUS_PUBLISHED);

        self::assertSame(2, $this->handler->getRuleCountFromGroup($copiedRuleGroup));
        self::assertSame(1, $this->handler->getRuleCountFromGroup($copiedSubGroup));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::copyRuleGroup
     */
    public function testCopyRuleGroupBelowItselfThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Rule group cannot be copied below itself or its children.');

        $ruleGroup = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(4, Value::STATUS_PUBLISHED);

        $this->handler->copyRuleGroup($ruleGroup, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::moveRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::moveRuleGroup
     */
    public function testMoveRuleGroup(): void
    {
        $ruleGroup = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(3, Value::STATUS_PUBLISHED);

        $movedRuleGroup = $this->handler->moveRuleGroup($ruleGroup, $targetGroup);

        self::assertSame($ruleGroup->id, $movedRuleGroup->id);
        self::assertSame($ruleGroup->uuid, $movedRuleGroup->uuid);
        self::assertSame($targetGroup->depth + 1, $movedRuleGroup->depth);
        self::assertSame($targetGroup->path . $movedRuleGroup->id . '/', $movedRuleGroup->path);
        self::assertSame($targetGroup->id, $movedRuleGroup->parentId);
        self::assertSame($targetGroup->uuid, $movedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $movedRuleGroup->name);
        self::assertSame($ruleGroup->description, $movedRuleGroup->description);
        self::assertSame($ruleGroup->priority, $movedRuleGroup->priority);
        self::assertSame($ruleGroup->enabled, $movedRuleGroup->enabled);
        self::assertSame($ruleGroup->status, $movedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleGroupId' => $movedRuleGroup->id,
                    'ruleGroupUuid' => $movedRuleGroup->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => 'b084d390-01ea-464b-8282-797b6ef9ef1e',
                    'value' => ['some_other_value'],
                ],
                [
                    'id' => 6,
                    'ruleGroupId' => $movedRuleGroup->id,
                    'ruleGroupUuid' => $movedRuleGroup->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => '46390b11-e077-4979-95cb-782575a9562b',
                    'value' => ['some_third_value'],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleGroupConditions($movedRuleGroup),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::moveRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::moveRuleGroup
     */
    public function testMoveRuleGroupWithPriority(): void
    {
        $ruleGroup = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(3, Value::STATUS_PUBLISHED);

        $movedRuleGroup = $this->handler->moveRuleGroup($ruleGroup, $targetGroup, 42);

        self::assertSame($ruleGroup->id, $movedRuleGroup->id);
        self::assertSame($ruleGroup->uuid, $movedRuleGroup->uuid);
        self::assertSame($targetGroup->depth + 1, $movedRuleGroup->depth);
        self::assertSame($targetGroup->path . $movedRuleGroup->id . '/', $movedRuleGroup->path);
        self::assertSame($targetGroup->id, $movedRuleGroup->parentId);
        self::assertSame($targetGroup->uuid, $movedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $movedRuleGroup->name);
        self::assertSame($ruleGroup->description, $movedRuleGroup->description);
        self::assertSame(42, $movedRuleGroup->priority);
        self::assertSame($ruleGroup->enabled, $movedRuleGroup->enabled);
        self::assertSame($ruleGroup->status, $movedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleGroupId' => $movedRuleGroup->id,
                    'ruleGroupUuid' => $movedRuleGroup->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => 'b084d390-01ea-464b-8282-797b6ef9ef1e',
                    'value' => ['some_other_value'],
                ],
                [
                    'id' => 6,
                    'ruleGroupId' => $movedRuleGroup->id,
                    'ruleGroupUuid' => $movedRuleGroup->uuid,
                    'status' => Value::STATUS_PUBLISHED,
                    'type' => 'condition1',
                    'uuid' => '46390b11-e077-4979-95cb-782575a9562b',
                    'value' => ['some_third_value'],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleGroupConditions($movedRuleGroup),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::moveRuleGroup
     */
    public function testMoveRuleGroupToSameGroupThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Rule group is already in specified target group.');

        $ruleGroup = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(1, Value::STATUS_PUBLISHED);

        $this->handler->moveRuleGroup($ruleGroup, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::moveRuleGroup
     */
    public function testMoveRuleGroupBelowItselfThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Rule group cannot be moved below itself or its children.');

        $ruleGroup = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
        $targetGroup = $this->handler->loadRuleGroup(4, Value::STATUS_PUBLISHED);

        $this->handler->moveRuleGroup($ruleGroup, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleGroupStatus
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addRuleGroupCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleGroupUuid
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionsData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupData
     */
    public function testCreateRuleGroupStatus(): void
    {
        $ruleGroup = $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
        $copiedRuleGroup = $this->handler->createRuleGroupStatus($ruleGroup, Value::STATUS_ARCHIVED);

        self::assertSame($ruleGroup->id, $copiedRuleGroup->id);
        self::assertSame($ruleGroup->uuid, $copiedRuleGroup->uuid);
        self::assertSame($ruleGroup->depth, $copiedRuleGroup->depth);
        self::assertSame($ruleGroup->path, $copiedRuleGroup->path);
        self::assertSame($ruleGroup->parentId, $copiedRuleGroup->parentId);
        self::assertSame($ruleGroup->parentUuid, $copiedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $copiedRuleGroup->name);
        self::assertSame($ruleGroup->description, $copiedRuleGroup->description);
        self::assertSame($ruleGroup->priority, $copiedRuleGroup->priority);
        self::assertSame($ruleGroup->enabled, $copiedRuleGroup->enabled);
        self::assertSame(Value::STATUS_ARCHIVED, $copiedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Value::STATUS_ARCHIVED,
                    'type' => 'condition1',
                    'uuid' => 'b084d390-01ea-464b-8282-797b6ef9ef1e',
                    'value' => [
                        'some_other_value',
                    ],
                ],
                [
                    'id' => 6,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Value::STATUS_ARCHIVED,
                    'type' => 'condition1',
                    'uuid' => '46390b11-e077-4979-95cb-782575a9562b',
                    'value' => [
                        'some_third_value',
                    ],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleGroupConditions($copiedRuleGroup),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleGroupConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleGroups
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRules
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadSubGroupIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadSubRuleIds
     */
    public function testDeleteRuleGroup(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "2"');

        $this->handler->deleteRuleGroup(2);

        $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleGroup
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleGroupConditions
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleGroups
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRules
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionData
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleGroupConditionIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadSubGroupIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadSubRuleIds
     */
    public function testDeleteRuleGroupInOneStatus(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "2"');

        $this->handler->deleteRuleGroup(2, Value::STATUS_DRAFT);

        // First, verify that NOT all rule group statuses are deleted
        try {
            $this->handler->loadRuleGroup(2, Value::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the rule group in draft status deleted other/all statuses.');
        }

        $this->handler->loadRuleGroup(2, Value::STATUS_DRAFT);
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

        $target = $this->withUuids(
            fn (): Target => $this->handler->addTarget(
                $this->handler->loadRule(1, Value::STATUS_PUBLISHED),
                $targetCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'id' => 21,
                'ruleId' => 1,
                'ruleUuid' => '26768324-03dd-5952-8a55-4b449d6cd634',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'target',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => '42',
            ],
            $this->exportObject($target),
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
            $targetUpdateStruct,
        );

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 1,
                'ruleUuid' => '26768324-03dd-5952-8a55-4b449d6cd634',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'route',
                'uuid' => 'c7c5cdca-02da-5ba5-ad9e-d25cbc4b1b46',
                'value' => 'my_new_route',
            ],
            $this->exportObject($target),
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::addRuleCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addRuleCondition
     */
    public function testAddRuleCondition(): void
    {
        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->type = 'condition';
        $conditionCreateStruct->value = ['param' => 'value'];

        $condition = $this->withUuids(
            fn (): RuleCondition => $this->handler->addRuleCondition(
                $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
                $conditionCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'id' => 7,
                'ruleId' => 3,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'condition',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => ['param' => 'value'],
            ],
            $this->exportObject($condition),
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::addRuleGroupCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addRuleGroupCondition
     */
    public function testAddRuleGroupCondition(): void
    {
        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->type = 'condition';
        $conditionCreateStruct->value = ['param' => 'value'];

        $condition = $this->withUuids(
            fn (): RuleGroupCondition => $this->handler->addRuleGroupCondition(
                $this->handler->loadRuleGroup(3, Value::STATUS_PUBLISHED),
                $conditionCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'id' => 7,
                'ruleGroupId' => 3,
                'ruleGroupUuid' => 'eb6311eb-24f6-4143-b476-99979a885a7e',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'condition',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => ['param' => 'value'],
            ],
            $this->exportObject($condition),
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
            $this->handler->loadRuleCondition(1, Value::STATUS_PUBLISHED),
            $conditionUpdateStruct,
        );

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 2,
                'ruleUuid' => '55622437-f700-5378-99c9-7dafe89a8fb6',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'route_parameter',
                'uuid' => '35f4594c-6674-5815-add6-07f288b79686',
                'value' => ['new_param' => 'new_value'],
            ],
            $this->exportObject($condition),
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
            $this->handler->loadRuleCondition(2, Value::STATUS_PUBLISHED),
        );

        $this->handler->loadRuleCondition(2, Value::STATUS_PUBLISHED);
    }
}
