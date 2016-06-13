<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
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
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareHandlers();

        $this->handler = $this->createLayoutResolverHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
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
        self::assertEquals(
            new Rule(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'enabled' => true,
                    'priority' => 0,
                    'comment' => 'My comment',
                    'status' => Rule::STATUS_PUBLISHED,
                )
            ),
            $this->handler->loadRule(1, Rule::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadRuleThrowsNotFoundException()
    {
        $this->handler->loadRule(999999, Rule::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesData
     */
    public function testLoadRules()
    {
        $rules = $this->handler->loadRules(Rule::STATUS_PUBLISHED);

        self::assertNotEmpty($rules);

        foreach ($rules as $rule) {
            self::assertInstanceOf(Rule::class, $rule);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesData
     */
    public function testLoadRulesInNonExistentStatus()
    {
        $rules = $this->handler->loadRules(Rule::STATUS_ARCHIVED);

        self::assertEmpty($rules);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getTargetSelectQuery
     */
    public function testLoadTarget()
    {
        self::assertEquals(
            new Target(
                array(
                    'id' => 1,
                    'ruleId' => 1,
                    'identifier' => 'route',
                    'value' => 'my_cool_route',
                    'status' => Rule::STATUS_PUBLISHED,
                )
            ),
            $this->handler->loadTarget(1, Rule::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadTargetThrowsNotFoundException()
    {
        $this->handler->loadTarget(999999, Rule::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleTargets
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testLoadRuleTargets()
    {
        $targets = $this->handler->loadRuleTargets(
            $this->handler->loadRule(1, Rule::STATUS_PUBLISHED)
        );

        self::assertNotEmpty($targets);

        foreach ($targets as $target) {
            self::assertInstanceOf(Target::class, $target);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getTargetCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getTargetCount
     */
    public function testGetTargetCount()
    {
        $targets = $this->handler->getTargetCount(
            $this->handler->loadRule(1, Rule::STATUS_PUBLISHED)
        );

        self::assertEquals(2, $targets);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getConditionSelectQuery
     */
    public function testLoadCondition()
    {
        self::assertEquals(
            new Condition(
                array(
                    'id' => 1,
                    'ruleId' => 2,
                    'identifier' => 'route_parameter',
                    'value' => array(
                        'some_param' => array(1, 2),
                    ),
                    'status' => Rule::STATUS_PUBLISHED,
                )
            ),
            $this->handler->loadCondition(1, Rule::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadConditionThrowsNotFoundException()
    {
        $this->handler->loadCondition(999999, Rule::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     */
    public function testLoadRuleConditions()
    {
        $conditions = $this->handler->loadRuleConditions(
            $this->handler->loadRule(2, Rule::STATUS_PUBLISHED)
        );

        self::assertNotEmpty($conditions);

        foreach ($conditions as $condition) {
            self::assertInstanceOf(Condition::class, $condition);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleExists()
    {
        self::assertTrue($this->handler->ruleExists(1, Rule::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleNotExists()
    {
        self::assertFalse($this->handler->ruleExists(999999, Rule::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleNotExistsInStatus()
    {
        self::assertFalse($this->handler->ruleExists(1, Rule::STATUS_ARCHIVED));
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

        $createdRule = $this->handler->createRule(
            $ruleCreateStruct,
            Rule::STATUS_DRAFT
        );

        self::assertInstanceOf(Rule::class, $createdRule);

        self::assertEquals(22, $createdRule->id);
        self::assertEquals(3, $createdRule->layoutId);
        self::assertEquals(5, $createdRule->priority);
        self::assertTrue($createdRule->enabled);
        self::assertEquals('My rule', $createdRule->comment);
        self::assertEquals(Rule::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRule()
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 15;
        $ruleUpdateStruct->priority = 5;
        $ruleUpdateStruct->comment = 'New comment';

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Rule::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        self::assertInstanceOf(Rule::class, $updatedRule);

        self::assertEquals(3, $updatedRule->id);
        self::assertEquals(15, $updatedRule->layoutId);
        self::assertEquals(5, $updatedRule->priority);
        self::assertEquals('New comment', $updatedRule->comment);
        self::assertEquals(Rule::STATUS_PUBLISHED, $updatedRule->status);
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
        $copiedRuleId = $this->handler->copyRule(5);

        self::assertEquals(22, $copiedRuleId);

        $copiedRule = $this->handler->loadRule($copiedRuleId, Rule::STATUS_PUBLISHED);

        self::assertInstanceOf(Rule::class, $copiedRule);
        self::assertEquals(2, $copiedRule->layoutId);
        self::assertEquals(4, $copiedRule->priority);
        self::assertFalse($copiedRule->enabled);
        self::assertNull($copiedRule->comment);
        self::assertEquals(Rule::STATUS_PUBLISHED, $copiedRule->status);

        self::assertEquals(
            array(
                new Target(
                    array(
                        'id' => 43,
                        'ruleId' => $copiedRuleId,
                        'identifier' => 'route_prefix',
                        'value' => 'my_second_cool_',
                        'status' => Rule::STATUS_PUBLISHED,
                    )
                ),
                new Target(
                    array(
                        'id' => 44,
                        'ruleId' => $copiedRuleId,
                        'identifier' => 'route_prefix',
                        'value' => 'my_third_cool_',
                        'status' => Rule::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->handler->loadRuleTargets($copiedRule)
        );

        self::assertEquals(
            array(
                new Condition(
                    array(
                        'id' => 5,
                        'ruleId' => $copiedRuleId,
                        'identifier' => 'siteaccess',
                        'value' => array('cro'),
                        'status' => Rule::STATUS_PUBLISHED,
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
            $this->handler->loadRule(3, Rule::STATUS_PUBLISHED),
            Rule::STATUS_ARCHIVED
        );

        self::assertInstanceOf(Rule::class, $copiedRule);

        self::assertEquals(3, $copiedRule->id);
        self::assertEquals(3, $copiedRule->layoutId);
        self::assertEquals(2, $copiedRule->priority);
        self::assertTrue($copiedRule->enabled);
        self::assertNull($copiedRule->comment);
        self::assertEquals(Rule::STATUS_ARCHIVED, $copiedRule->status);

        self::assertEquals(
            array(
                new Target(
                    array(
                        'id' => 5,
                        'ruleId' => 3,
                        'identifier' => 'route',
                        'value' => 'my_fourth_cool_route',
                        'status' => Rule::STATUS_ARCHIVED,
                    )
                ),
                new Target(
                    array(
                        'id' => 6,
                        'ruleId' => 3,
                        'identifier' => 'route',
                        'value' => 'my_fifth_cool_route',
                        'status' => Rule::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->handler->loadRuleTargets($copiedRule)
        );

        self::assertEquals(
            array(
                new Condition(
                    array(
                        'id' => 2,
                        'ruleId' => 3,
                        'identifier' => 'route_parameter',
                        'value' => array(
                            'some_param' => array(3, 4),
                        ),
                        'status' => Rule::STATUS_ARCHIVED,
                    )
                ),
                new Condition(
                    array(
                        'id' => 3,
                        'ruleId' => 3,
                        'identifier' => 'route_parameter',
                        'value' => array(
                            'some_other_param' => array(5, 6),
                        ),
                        'status' => Rule::STATUS_ARCHIVED,
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

        $this->handler->loadRule(3, Rule::STATUS_PUBLISHED);
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
        $this->handler->deleteRule(5, Rule::STATUS_DRAFT);

        // First, verify that NOT all rule statuses are deleted
        try {
            $this->handler->loadRule(5, Rule::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the rule in draft status deleted other/all statuses.');
        }

        $this->handler->loadRule(5, Rule::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::enableRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleData
     */
    public function testEnableRule()
    {
        $this->handler->enableRule(
            $this->handler->loadRule(5, Rule::STATUS_PUBLISHED)
        );

        self::assertTrue($this->handler->loadRule(5, Rule::STATUS_PUBLISHED)->enabled);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::disableRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleData
     */
    public function testDisableRule()
    {
        $this->handler->disableRule(
            $this->handler->loadRule(1, Rule::STATUS_PUBLISHED)
        );

        self::assertFalse($this->handler->loadRule(1, Rule::STATUS_PUBLISHED)->enabled);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::addTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     */
    public function testAddTarget()
    {
        $targetCreateStruct = new TargetCreateStruct();
        $targetCreateStruct->identifier = 'target';
        $targetCreateStruct->value = '42';

        self::assertEquals(
            new Target(
                array(
                    'id' => 43,
                    'ruleId' => 1,
                    'identifier' => 'target',
                    'value' => '42',
                    'status' => Rule::STATUS_PUBLISHED,
                )
            ),
            $this->handler->addTarget(
                $this->handler->loadRule(1, Rule::STATUS_PUBLISHED),
                $targetCreateStruct
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
        $this->handler->deleteTarget(
            $this->handler->loadTarget(2, Rule::STATUS_PUBLISHED)
        );

        $this->handler->loadTarget(2, Rule::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::addCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     */
    public function testAddCondition()
    {
        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->identifier = 'condition';
        $conditionCreateStruct->value = array('param' => 'value');

        self::assertEquals(
            new Condition(
                array(
                    'id' => 5,
                    'ruleId' => 3,
                    'identifier' => 'condition',
                    'value' => array('param' => 'value'),
                    'status' => Rule::STATUS_PUBLISHED,
                )
            ),
            $this->handler->addCondition(
                $this->handler->loadRule(3, Rule::STATUS_PUBLISHED),
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

        self::assertEquals(
            new Condition(
                array(
                    'id' => 1,
                    'ruleId' => 2,
                    'identifier' => 'route_parameter',
                    'value' => array('new_param' => 'new_value'),
                    'status' => Rule::STATUS_PUBLISHED,
                )
            ),
            $this->handler->updateCondition(
                $this->handler->loadCondition(1, Rule::STATUS_PUBLISHED),
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
            $this->handler->loadCondition(2, Rule::STATUS_PUBLISHED)
        );

        $this->handler->loadCondition(2, Rule::STATUS_PUBLISHED);
    }
}
