<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class LayoutResolverHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler
     */
    protected $layoutHandler;

    /**
     * Sets up the tests.
     */
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleSelectQuery
     */
    public function testLoadRule()
    {
        $this->assertEquals(
            new Rule(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'enabled' => true,
                    'priority' => 20,
                    'comment' => 'My comment',
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
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

        $this->assertNotEmpty($rules);

        foreach ($rules as $index => $rule) {
            $this->assertInstanceOf(Rule::class, $rule);
            if ($index > 0) {
                $this->assertLessThanOrEqual($rules[$index - 1]->priority, $rules[$index]->priority);
            }
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesData
     */
    public function testLoadRulesInNonExistentStatus()
    {
        $rules = $this->handler->loadRules(Value::STATUS_ARCHIVED);

        $this->assertEmpty($rules);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleCount
     */
    public function testGetRuleCount()
    {
        $rules = $this->handler->getRuleCount(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );

        $this->assertEquals(3, $rules);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getTargetSelectQuery
     */
    public function testLoadTarget()
    {
        $this->assertEquals(
            new Target(
                array(
                    'id' => 1,
                    'ruleId' => 1,
                    'type' => 'route',
                    'value' => 'my_cool_route',
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->handler->loadTarget(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getConditionSelectQuery
     */
    public function testLoadCondition()
    {
        $this->assertEquals(
            new Condition(
                array(
                    'id' => 1,
                    'ruleId' => 2,
                    'type' => 'route_parameter',
                    'value' => array(
                        'parameter_name' => 'some_param',
                        'parameter_values' => array(1, 2),
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->handler->loadCondition(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
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

        $this->assertEquals(22, $createdRule->id);
        $this->assertEquals(3, $createdRule->layoutId);
        $this->assertEquals(5, $createdRule->priority);
        $this->assertTrue($createdRule->enabled);
        $this->assertEquals('My rule', $createdRule->comment);
        $this->assertEquals(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     */
    public function testCreateRuleWithNoPriority()
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $createdRule = $this->handler->createRule($ruleCreateStruct);

        $this->assertInstanceOf(Rule::class, $createdRule);

        $this->assertEquals(22, $createdRule->id);
        $this->assertNull($createdRule->layoutId);
        $this->assertEquals(0, $createdRule->priority);
        $this->assertFalse($createdRule->enabled);
        $this->assertEquals(null, $createdRule->comment);
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
                array(
                    'enabled' => false,
                    'priority' => 50,
                )
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
        $this->assertEquals(16, $updatedRule->priority);
        $this->assertTrue($updatedRule->enabled);
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::copyRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     */
    public function testCopyRule()
    {
        $copiedRule = $this->handler->copyRule(
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED)
        );

        $this->assertInstanceOf(Rule::class, $copiedRule);
        $this->assertEquals(22, $copiedRule->id);
        $this->assertEquals(2, $copiedRule->layoutId);
        $this->assertEquals(16, $copiedRule->priority);
        $this->assertTrue($copiedRule->enabled);
        $this->assertNull($copiedRule->comment);
        $this->assertEquals(Value::STATUS_PUBLISHED, $copiedRule->status);

        $this->assertEquals(
            array(
                new Target(
                    array(
                        'id' => 43,
                        'ruleId' => $copiedRule->id,
                        'type' => 'route_prefix',
                        'value' => 'my_second_cool_',
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
                new Target(
                    array(
                        'id' => 44,
                        'ruleId' => $copiedRule->id,
                        'type' => 'route_prefix',
                        'value' => 'my_third_cool_',
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->handler->loadRuleTargets($copiedRule)
        );

        $this->assertEquals(
            array(
                new Condition(
                    array(
                        'id' => 5,
                        'ruleId' => $copiedRule->id,
                        'type' => 'ez_site_access',
                        'value' => array('cro'),
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->handler->loadRuleConditions($copiedRule)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
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
        $this->assertEquals(18, $copiedRule->priority);
        $this->assertTrue($copiedRule->enabled);
        $this->assertNull($copiedRule->comment);
        $this->assertEquals(Value::STATUS_ARCHIVED, $copiedRule->status);

        $this->assertEquals(
            array(
                new Target(
                    array(
                        'id' => 5,
                        'ruleId' => 3,
                        'type' => 'route',
                        'value' => 'my_fourth_cool_route',
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
                new Target(
                    array(
                        'id' => 6,
                        'ruleId' => 3,
                        'type' => 'route',
                        'value' => 'my_fifth_cool_route',
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->handler->loadRuleTargets($copiedRule)
        );

        $this->assertEquals(
            array(
                new Condition(
                    array(
                        'id' => 2,
                        'ruleId' => 3,
                        'type' => 'route_parameter',
                        'value' => array(
                            'parameter_name' => 'some_param',
                            'parameter_values' => array(3, 4),
                        ),
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
                new Condition(
                    array(
                        'id' => 3,
                        'ruleId' => 3,
                        'type' => 'route_parameter',
                        'value' => array(
                            'parameter_name' => 'some_other_param',
                            'parameter_values' => array(5, 6),
                        ),
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->handler->loadRuleConditions($copiedRule)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleTargets
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRule
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteRule()
    {
        $this->handler->deleteRule(3);

        $this->handler->loadRule(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleTargets
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRule
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
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
                array(
                    'id' => 43,
                    'ruleId' => 1,
                    'type' => 'target',
                    'value' => '42',
                    'status' => Value::STATUS_PUBLISHED,
                )
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
                array(
                    'id' => 1,
                    'ruleId' => 1,
                    'type' => 'route',
                    'value' => 'my_new_route',
                    'status' => Value::STATUS_PUBLISHED,
                )
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
        $conditionCreateStruct->value = array('param' => 'value');

        $this->assertEquals(
            new Condition(
                array(
                    'id' => 5,
                    'ruleId' => 3,
                    'type' => 'condition',
                    'value' => array('param' => 'value'),
                    'status' => Value::STATUS_PUBLISHED,
                )
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
        $conditionUpdateStruct->value = array('new_param' => 'new_value');

        $this->assertEquals(
            new Condition(
                array(
                    'id' => 1,
                    'ruleId' => 2,
                    'type' => 'route_parameter',
                    'value' => array('new_param' => 'new_value'),
                    'status' => Value::STATUS_PUBLISHED,
                )
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
     */
    public function testDeleteCondition()
    {
        $this->handler->deleteCondition(
            $this->handler->loadCondition(2, Value::STATUS_PUBLISHED)
        );

        $this->handler->loadCondition(2, Value::STATUS_PUBLISHED);
    }
}
