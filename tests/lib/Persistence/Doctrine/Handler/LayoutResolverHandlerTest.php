<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\TestCase;

final class LayoutResolverHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    public function setUp()
    {
        $this->createDatabase();

        $this->handler = $this->createLayoutResolverHandler();
        $this->layoutHandler = $this->createLayoutHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     */
    public function testLoadRule()
    {
        $this->assertEquals(
            new Rule(
                [
                    'id' => 1,
                    'layoutId' => 1,
                    'enabled' => true,
                    'priority' => 9,
                    'comment' => 'My comment',
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "999999"
     */
    public function testLoadRuleThrowsNotFoundException()
    {
        $this->handler->loadRule(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesData
     */
    public function testLoadRules()
    {
        $rules = $this->handler->loadRules(Value::STATUS_PUBLISHED);

        $this->assertCount(12, $rules);

        $previousPriority = null;
        foreach ($rules as $index => $rule) {
            $this->assertInstanceOf(Rule::class, $rule);

            if ($index > 0) {
                $this->assertLessThanOrEqual($previousPriority, $rule->priority);
            }

            $previousPriority = $rule->priority;
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesData
     */
    public function testLoadRulesWithLayout()
    {
        $rules = $this->handler->loadRules(
            Value::STATUS_PUBLISHED,
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(2, $rules);

        $previousPriority = null;
        foreach ($rules as $index => $rule) {
            $this->assertInstanceOf(Rule::class, $rule);

            if ($index > 0) {
                $this->assertLessThanOrEqual($previousPriority, $rule->priority);
            }

            $previousPriority = $rule->priority;
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleCount
     */
    public function testGetRuleCount()
    {
        $rules = $this->handler->getRuleCount();

        $this->assertEquals(12, $rules);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleCount
     */
    public function testGetRuleCountWithLayout()
    {
        $rules = $this->handler->getRuleCount(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );

        $this->assertEquals(2, $rules);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getTargetSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     */
    public function testLoadTarget()
    {
        $this->assertEquals(
            new Target(
                [
                    'id' => 1,
                    'ruleId' => 1,
                    'type' => 'route',
                    'value' => 'my_cool_route',
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            $this->handler->loadTarget(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find target with identifier "999999"
     */
    public function testLoadTargetThrowsNotFoundException()
    {
        $this->handler->loadTarget(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleTargets
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testLoadRuleTargets()
    {
        $targets = $this->handler->loadRuleTargets(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED)
        );

        $this->assertNotEmpty($targets);

        foreach ($targets as $target) {
            $this->assertInstanceOf(Target::class, $target);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getTargetCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getTargetCount
     */
    public function testGetTargetCount()
    {
        $targets = $this->handler->getTargetCount(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED)
        );

        $this->assertEquals(2, $targets);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getConditionSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     */
    public function testLoadCondition()
    {
        $this->assertEquals(
            new Condition(
                [
                    'id' => 1,
                    'ruleId' => 2,
                    'type' => 'route_parameter',
                    'value' => [
                        'parameter_name' => 'some_param',
                        'parameter_values' => [1, 2],
                    ],
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            $this->handler->loadCondition(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find condition with identifier "999999"
     */
    public function testLoadConditionThrowsNotFoundException()
    {
        $this->handler->loadCondition(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     */
    public function testLoadRuleConditions()
    {
        $conditions = $this->handler->loadRuleConditions(
            $this->handler->loadRule(2, Value::STATUS_PUBLISHED)
        );

        $this->assertNotEmpty($conditions);

        foreach ($conditions as $condition) {
            $this->assertInstanceOf(Condition::class, $condition);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleExists()
    {
        $this->assertTrue($this->handler->ruleExists(1, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleNotExists()
    {
        $this->assertFalse($this->handler->ruleExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleNotExistsInStatus()
    {
        $this->assertFalse($this->handler->ruleExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRulePriority
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestRulePriority
     */
    public function testCreateRule()
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->layoutId = 3;
        $ruleCreateStruct->priority = 5;
        $ruleCreateStruct->enabled = true;
        $ruleCreateStruct->comment = 'My rule';
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $createdRule = $this->handler->createRule($ruleCreateStruct);

        $this->assertInstanceOf(Rule::class, $createdRule);

        $this->assertEquals(13, $createdRule->id);
        $this->assertEquals(3, $createdRule->layoutId);
        $this->assertEquals(5, $createdRule->priority);
        $this->assertTrue($createdRule->enabled);
        $this->assertEquals('My rule', $createdRule->comment);
        $this->assertEquals(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRulePriority
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestRulePriority
     */
    public function testCreateRuleWithNoPriority()
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $createdRule = $this->handler->createRule($ruleCreateStruct);

        $this->assertInstanceOf(Rule::class, $createdRule);

        $this->assertEquals(13, $createdRule->id);
        $this->assertNull($createdRule->layoutId);
        $this->assertEquals(-12, $createdRule->priority);
        $this->assertFalse($createdRule->enabled);
        $this->assertEquals('', $createdRule->comment);
        $this->assertEquals(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRulePriority
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestRulePriority
     */
    public function testCreateRuleWithNoPriorityAndNoRules()
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

        $this->assertInstanceOf(Rule::class, $createdRule);

        $this->assertEquals(0, $createdRule->priority);
        $this->assertEquals(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRule()
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 15;
        $ruleUpdateStruct->comment = 'New comment';

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);

        $this->assertEquals(3, $updatedRule->id);
        $this->assertEquals(15, $updatedRule->layoutId);
        $this->assertEquals('New comment', $updatedRule->comment);
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRuleWithRemovalOfLinkedLayout()
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 0;

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);

        $this->assertEquals(3, $updatedRule->id);
        $this->assertNull($updatedRule->layoutId);
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRuleWithDefaultValues()
    {
        $ruleUpdateStruct = new RuleUpdateStruct();

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);

        $this->assertEquals(3, $updatedRule->id);
        $this->assertEquals(3, $updatedRule->layoutId);
        $this->assertNull($updatedRule->comment);
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleMetadata
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleData
     */
    public function testUpdateRuleMetadata()
    {
        $updatedRule = $this->handler->updateRuleMetadata(
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED),
            new RuleMetadataUpdateStruct(
                [
                    'enabled' => false,
                    'priority' => 50,
                ]
            )
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertEquals(50, $updatedRule->priority);
        $this->assertFalse($updatedRule->enabled);
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleMetadata
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleData
     */
    public function testUpdateRuleMetadataWithDefaultValues()
    {
        $updatedRule = $this->handler->updateRuleMetadata(
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED),
            new RuleMetadataUpdateStruct()
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertEquals(5, $updatedRule->priority);
        $this->assertTrue($updatedRule->enabled);
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::copyRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testCopyRule()
    {
        $copiedRule = $this->handler->copyRule(
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED)
        );

        $this->assertInstanceOf(Rule::class, $copiedRule);
        $this->assertEquals(13, $copiedRule->id);
        $this->assertEquals(2, $copiedRule->layoutId);
        $this->assertEquals(5, $copiedRule->priority);
        $this->assertTrue($copiedRule->enabled);
        $this->assertNull($copiedRule->comment);
        $this->assertEquals(Value::STATUS_PUBLISHED, $copiedRule->status);

        $this->assertEquals(
            [
                new Target(
                    [
                        'id' => 21,
                        'ruleId' => $copiedRule->id,
                        'type' => 'route_prefix',
                        'value' => 'my_second_cool_',
                        'status' => Value::STATUS_PUBLISHED,
                    ]
                ),
                new Target(
                    [
                        'id' => 22,
                        'ruleId' => $copiedRule->id,
                        'type' => 'route_prefix',
                        'value' => 'my_third_cool_',
                        'status' => Value::STATUS_PUBLISHED,
                    ]
                ),
            ],
            $this->handler->loadRuleTargets($copiedRule)
        );

        $this->assertEquals(
            [
                new Condition(
                    [
                        'id' => 5,
                        'ruleId' => $copiedRule->id,
                        'type' => 'my_condition',
                        'value' => ['some_value'],
                        'status' => Value::STATUS_PUBLISHED,
                    ]
                ),
            ],
            $this->handler->loadRuleConditions($copiedRule)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testCreateRuleStatus()
    {
        $copiedRule = $this->handler->createRuleStatus(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            Value::STATUS_ARCHIVED
        );

        $this->assertInstanceOf(Rule::class, $copiedRule);

        $this->assertEquals(3, $copiedRule->id);
        $this->assertEquals(3, $copiedRule->layoutId);
        $this->assertEquals(7, $copiedRule->priority);
        $this->assertTrue($copiedRule->enabled);
        $this->assertNull($copiedRule->comment);
        $this->assertEquals(Value::STATUS_ARCHIVED, $copiedRule->status);

        $this->assertEquals(
            [
                new Target(
                    [
                        'id' => 5,
                        'ruleId' => 3,
                        'type' => 'route',
                        'value' => 'my_fourth_cool_route',
                        'status' => Value::STATUS_ARCHIVED,
                    ]
                ),
                new Target(
                    [
                        'id' => 6,
                        'ruleId' => 3,
                        'type' => 'route',
                        'value' => 'my_fifth_cool_route',
                        'status' => Value::STATUS_ARCHIVED,
                    ]
                ),
            ],
            $this->handler->loadRuleTargets($copiedRule)
        );

        $this->assertEquals(
            [
                new Condition(
                    [
                        'id' => 2,
                        'ruleId' => 3,
                        'type' => 'route_parameter',
                        'value' => [
                            'parameter_name' => 'some_param',
                            'parameter_values' => [3, 4],
                        ],
                        'status' => Value::STATUS_ARCHIVED,
                    ]
                ),
                new Condition(
                    [
                        'id' => 3,
                        'ruleId' => 3,
                        'type' => 'route_parameter',
                        'value' => [
                            'parameter_name' => 'some_other_param',
                            'parameter_values' => [5, 6],
                        ],
                        'status' => Value::STATUS_ARCHIVED,
                    ]
                ),
            ],
            $this->handler->loadRuleConditions($copiedRule)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleTargets
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "3"
     */
    public function testDeleteRule()
    {
        $this->handler->deleteRule(3);

        $this->handler->loadRule(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleTargets
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "5"
     */
    public function testDeleteRuleInOneStatus()
    {
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::addTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     */
    public function testAddTarget()
    {
        $targetCreateStruct = new TargetCreateStruct();
        $targetCreateStruct->type = 'target';
        $targetCreateStruct->value = '42';

        $this->assertEquals(
            new Target(
                [
                    'id' => 21,
                    'ruleId' => 1,
                    'type' => 'target',
                    'value' => '42',
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            $this->handler->addTarget(
                $this->handler->loadRule(1, Value::STATUS_PUBLISHED),
                $targetCreateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateTarget
     */
    public function testUpdateTarget()
    {
        $targetUpdateStruct = new TargetUpdateStruct();
        $targetUpdateStruct->value = 'my_new_route';

        $this->assertEquals(
            new Target(
                [
                    'id' => 1,
                    'ruleId' => 1,
                    'type' => 'route',
                    'value' => 'my_new_route',
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            $this->handler->updateTarget(
                $this->handler->loadTarget(1, Value::STATUS_PUBLISHED),
                $targetUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteTarget
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find target with identifier "2"
     */
    public function testDeleteTarget()
    {
        $target = $this->handler->loadTarget(2, Value::STATUS_PUBLISHED);

        $this->handler->deleteTarget($target);

        $this->handler->loadTarget(2, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::addCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     */
    public function testAddCondition()
    {
        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->type = 'condition';
        $conditionCreateStruct->value = ['param' => 'value'];

        $this->assertEquals(
            new Condition(
                [
                    'id' => 5,
                    'ruleId' => 3,
                    'type' => 'condition',
                    'value' => ['param' => 'value'],
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            $this->handler->addCondition(
                $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
                $conditionCreateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateCondition
     */
    public function testUpdateCondition()
    {
        $conditionUpdateStruct = new ConditionUpdateStruct();
        $conditionUpdateStruct->value = ['new_param' => 'new_value'];

        $this->assertEquals(
            new Condition(
                [
                    'id' => 1,
                    'ruleId' => 2,
                    'type' => 'route_parameter',
                    'value' => ['new_param' => 'new_value'],
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            $this->handler->updateCondition(
                $this->handler->loadCondition(1, Value::STATUS_PUBLISHED),
                $conditionUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteCondition
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find condition with identifier "2"
     */
    public function testDeleteCondition()
    {
        $this->handler->deleteCondition(
            $this->handler->loadCondition(2, Value::STATUS_PUBLISHED)
        );

        $this->handler->loadCondition(2, Value::STATUS_PUBLISHED);
    }
}
