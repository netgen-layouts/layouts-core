<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft;
use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Exception\NotFoundException;

abstract class LayoutResolverServiceTest extends ServiceTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverValidatorMock;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->layoutResolverValidatorMock = $this->getMockBuilder(LayoutResolverValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutResolverService = $this->createLayoutResolverService($this->layoutResolverValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRule
     */
    public function testLoadRule()
    {
        $rule = $this->layoutResolverService->loadRule(3);

        self::assertInstanceOf(Rule::class, $rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRule
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadRuleThrowsNotFoundException()
    {
        $this->layoutResolverService->loadRule(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRuleDraft
     */
    public function testLoadRuleDraft()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        self::assertInstanceOf(RuleDraft::class, $rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRuleDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadRuleDraftThrowsNotFoundException()
    {
        $this->layoutResolverService->loadRuleDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRules
     */
    public function testLoadRules()
    {
        $rules = $this->layoutResolverService->loadRules();

        self::assertNotEmpty($rules);

        foreach ($rules as $rule) {
            self::assertInstanceOf(Rule::class, $rule);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::matchRules
     */
    public function testMatchRules()
    {
        $rules = $this->layoutResolverService->matchRules('route', 'my_cool_route');

        self::assertNotEmpty($rules);

        foreach ($rules as $rule) {
            self::assertInstanceOf(Rule::class, $rule);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadTarget
     */
    public function testLoadTarget()
    {
        $target = $this->layoutResolverService->loadTarget(7);

        self::assertInstanceOf(Target::class, $target);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadTarget
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadTargetThrowsNotFoundException()
    {
        $this->layoutResolverService->loadTarget(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadTargetDraft
     */
    public function testLoadTargetDraft()
    {
        $target = $this->layoutResolverService->loadTargetDraft(9);

        self::assertInstanceOf(TargetDraft::class, $target);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadTargetDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadTargetDraftThrowsNotFoundException()
    {
        $this->layoutResolverService->loadTargetDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadCondition
     */
    public function testLoadCondition()
    {
        $condition = $this->layoutResolverService->loadCondition(1);

        self::assertInstanceOf(Condition::class, $condition);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadCondition
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadConditionThrowsNotFoundException()
    {
        $this->layoutResolverService->loadCondition(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadConditionDraft
     */
    public function testLoadConditionDraft()
    {
        $condition = $this->layoutResolverService->loadConditionDraft(4);

        self::assertInstanceOf(ConditionDraft::class, $condition);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadConditionDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadConditionDraftThrowsNotFoundException()
    {
        $this->layoutResolverService->loadConditionDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createRule
     */
    public function testCreateRule()
    {
        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();

        $createdRule = $this->layoutResolverService->createRule($ruleCreateStruct);

        self::assertInstanceOf(RuleDraft::class, $createdRule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRule()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 50;
        $ruleUpdateStruct->priority = 6;
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        self::assertInstanceOf(RuleDraft::class, $updatedRule);
        self::assertEquals(50, $updatedRule->getLayoutId());
        self::assertEquals(6, $updatedRule->getPriority());
        self::assertEquals('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRule()
    {
        $rule = $this->layoutResolverService->loadRule(2);
        $copiedRule = $this->layoutResolverService->copyRule($rule);

        self::assertInstanceOf(Rule::class, $copiedRule);
        self::assertEquals(22, $copiedRule->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraft()
    {
        $rule = $this->layoutResolverService->loadRule(3);

        $draftRule = $this->layoutResolverService->createDraft($rule);

        self::assertInstanceOf(RuleDraft::class, $draftRule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists()
    {
        $rule = $this->layoutResolverService->loadRule(3);
        $this->layoutResolverService->createDraft($rule);

        $this->layoutResolverService->createDraft($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDiscardDraft()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);
        $this->layoutResolverService->discardDraft($rule);

        $this->layoutResolverService->loadRuleDraft($rule->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRule()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);
        $publishedRule = $this->layoutResolverService->publishRule($rule);

        self::assertInstanceOf(Rule::class, $publishedRule);
        self::assertEquals(Rule::STATUS_PUBLISHED, $publishedRule->getStatus());

        try {
            $this->layoutResolverService->loadRuleDraft($rule->getId());
            self::fail('Draft rule still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteRule
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteRule()
    {
        $rule = $this->layoutResolverService->loadRule(5);

        $this->layoutResolverService->deleteRule($rule);

        $this->layoutResolverService->loadRule($rule->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     */
    public function testEnableRule()
    {
        $rule = $this->layoutResolverService->loadRule(5);

        $this->layoutResolverService->enableRule($rule);

        $enabledRule = $this->layoutResolverService->loadRule($rule->getId());
        self::assertTrue($enabledRule->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testEnableRuleThrowsBadStateExceptionIfRuleIsAlreadyEnabled()
    {
        $rule = $this->layoutResolverService->loadRule(1);

        $this->layoutResolverService->enableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testEnableRuleThrowsBadStateExceptionIfRuleHasNoLayout()
    {
        $rule = $this->layoutResolverService->loadRule(15);

        $this->layoutResolverService->enableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testEnableRuleThrowsBadStateExceptionIfRuleHasNoTargets()
    {
        $rule = $this->layoutResolverService->loadRule(16);

        $this->layoutResolverService->enableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     */
    public function testDisableRule()
    {
        $rule = $this->layoutResolverService->loadRule(1);

        $this->layoutResolverService->disableRule($rule);

        $disabledRule = $this->layoutResolverService->loadRule($rule->getId());
        self::assertFalse($disabledRule->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDisableRuleThrowsBadStateExceptionIfRuleIsAlreadyDisabled()
    {
        $rule = $this->layoutResolverService->loadRule(5);

        $this->layoutResolverService->disableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     */
    public function testAddTarget()
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route_prefix',
            'some_route_'
        );

        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $createdTarget = $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );

        self::assertInstanceOf(TargetDraft::class, $createdTarget);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddTargetOfDifferentKindThrowsBadStateException()
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route',
            'some_route'
        );

        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteTarget
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteTarget()
    {
        $target = $this->layoutResolverService->loadTargetDraft(9);
        $this->layoutResolverService->deleteTarget($target);

        $this->layoutResolverService->loadTargetDraft($target->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addCondition
     */
    public function testAddCondition()
    {
        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'siteaccess',
            'cro'
        );

        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $createdCondition = $this->layoutResolverService->addCondition(
            $rule,
            $conditionCreateStruct
        );

        self::assertInstanceOf(ConditionDraft::class, $createdCondition);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateCondition
     */
    public function testUpdateCondition()
    {
        $condition = $this->layoutResolverService->loadConditionDraft(4);

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct('new_value');
        $updatedCondition = $this->layoutResolverService->updateCondition($condition, $conditionUpdateStruct);

        self::assertInstanceOf(ConditionDraft::class, $updatedCondition);

        self::assertEquals('new_value', $updatedCondition->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteCondition
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteCondition()
    {
        $condition = $this->layoutResolverService->loadConditionDraft(4);
        $this->layoutResolverService->deleteCondition($condition);

        $this->layoutResolverService->loadConditionDraft($condition->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newRuleCreateStruct
     */
    public function testNewRuleCreateStruct()
    {
        self::assertEquals(
            new RuleCreateStruct(),
            $this->layoutResolverService->newRuleCreateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newRuleUpdateStruct
     */
    public function testNewRuleUpdateStruct()
    {
        self::assertEquals(
            new RuleUpdateStruct(),
            $this->layoutResolverService->newRuleUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newTargetCreateStruct
     */
    public function testNewTargetCreateStruct()
    {
        self::assertEquals(
            new TargetCreateStruct(
                array(
                    'identifier' => 'target',
                    'value' => '42',
                )
            ),
            $this->layoutResolverService->newTargetCreateStruct('target', '42')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newConditionCreateStruct
     */
    public function testNewConditionCreateStruct()
    {
        self::assertEquals(
            new ConditionCreateStruct(
                array(
                    'identifier' => 'condition',
                    'value' => '42',
                )
            ),
            $this->layoutResolverService->newConditionCreateStruct('condition', '42')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newConditionUpdateStruct
     */
    public function testNewConditionUpdateStruct()
    {
        self::assertEquals(
            new ConditionUpdateStruct(
                array(
                    'value' => '42',
                )
            ),
            $this->layoutResolverService->newConditionUpdateStruct('42')
        );
    }
}
