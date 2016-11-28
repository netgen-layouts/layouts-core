<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\NotFoundException;

abstract class LayoutResolverServiceTest extends ServiceTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutValidatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverValidatorMock;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->layoutValidatorMock = $this->createMock(LayoutValidator::class);
        $this->layoutResolverValidatorMock = $this->createMock(LayoutResolverValidator::class);

        $this->layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $this->layoutResolverService = $this->createLayoutResolverService($this->layoutResolverValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRule
     */
    public function testLoadRule()
    {
        $rule = $this->layoutResolverService->loadRule(3);

        $this->assertTrue($rule->isPublished());
        $this->assertInstanceOf(Rule::class, $rule);
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

        $this->assertFalse($rule->isPublished());
        $this->assertInstanceOf(Rule::class, $rule);
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

        $this->assertNotEmpty($rules);

        foreach ($rules as $rule) {
            $this->assertTrue($rule->isPublished());
            $this->assertInstanceOf(Rule::class, $rule);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::getRuleCount
     */
    public function testGetRuleCount()
    {
        $ruleCount = $this->layoutResolverService->getRuleCount(
            $this->layoutService->loadLayout(1)
        );

        $this->assertEquals(3, $ruleCount);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::getRuleCount
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testGetRuleCountThrowsBadStateExceptionWithNonPublishedLayout()
    {
        $this->layoutResolverService->getRuleCount(
            $this->layoutService->loadLayoutDraft(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::matchRules
     */
    public function testMatchRules()
    {
        $rules = $this->layoutResolverService->matchRules('route', 'my_cool_route');

        $this->assertNotEmpty($rules);

        foreach ($rules as $rule) {
            $this->assertTrue($rule->isPublished());
            $this->assertInstanceOf(Rule::class, $rule);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadTarget
     */
    public function testLoadTarget()
    {
        $target = $this->layoutResolverService->loadTarget(7);

        $this->assertTrue($target->isPublished());
        $this->assertInstanceOf(Target::class, $target);
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

        $this->assertFalse($target->isPublished());
        $this->assertInstanceOf(Target::class, $target);
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

        $this->assertTrue($condition->isPublished());
        $this->assertInstanceOf(Condition::class, $condition);
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

        $this->assertFalse($condition->isPublished());
        $this->assertInstanceOf(Condition::class, $condition);
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

        $this->assertFalse($createdRule->isPublished());
        $this->assertInstanceOf(Rule::class, $createdRule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRule()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 3;
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        $this->assertFalse($updatedRule->isPublished());
        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertTrue($updatedRule->getLayout()->isPublished());
        $this->assertInstanceOf(Layout::class, $updatedRule->getLayout());
        $this->assertEquals(3, $updatedRule->getLayout()->getId());
        $this->assertEquals('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRuleWithNoLayout()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        $this->assertFalse($updatedRule->isPublished());
        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertTrue($rule->getLayout()->isPublished());
        $this->assertInstanceOf(Layout::class, $rule->getLayout());
        $this->assertEquals(2, $updatedRule->getLayout()->getId());
        $this->assertEquals('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRuleWithEmptyLayout()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 0;
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        $this->assertFalse($updatedRule->isPublished());
        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertNull($updatedRule->getLayout());
        $this->assertEquals('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateRuleThrowsBadStateExceptionWithNonDraftRule()
    {
        $rule = $this->layoutResolverService->loadRule(5);

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 3;
        $ruleUpdateStruct->comment = 'Updated comment';

        $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRuleMetadata
     */
    public function testUpdateRuleMetadata()
    {
        $rule = $this->layoutResolverService->loadRule(4);

        $updatedRule = $this->layoutResolverService->updateRuleMetadata(
            $rule,
            new RuleMetadataUpdateStruct(
                array(
                    'priority' => 50,
                )
            )
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertEquals(50, $updatedRule->getPriority());
        $this->assertTrue($updatedRule->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRuleMetadata
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateRuleMetadataThrowsBadStateExceptionWithNonPublishedRule()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        $this->layoutResolverService->updateRuleMetadata(
            $rule,
            new RuleMetadataUpdateStruct(
                array(
                    'priority' => 50,
                )
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRule()
    {
        $rule = $this->layoutResolverService->loadRule(2);
        $copiedRule = $this->layoutResolverService->copyRule($rule);

        $this->assertEquals($rule->isPublished(), $copiedRule->isPublished());
        $this->assertInstanceOf(Rule::class, $copiedRule);
        $this->assertEquals(22, $copiedRule->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraft()
    {
        $rule = $this->layoutResolverService->loadRule(3);

        $draftRule = $this->layoutResolverService->createDraft($rule);

        $this->assertFalse($draftRule->isPublished());
        $this->assertInstanceOf(Rule::class, $draftRule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateDraftThrowsBadStateExceptionWithNonPublishedRule()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        $this->layoutResolverService->createDraft($rule);
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
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDiscardDraftThrowsBadStateExceptionWithNonDraftRule()
    {
        $rule = $this->layoutResolverService->loadRule(5);
        $this->layoutResolverService->discardDraft($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRule()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);
        $publishedRule = $this->layoutResolverService->publishRule($rule);

        $this->assertInstanceOf(Rule::class, $publishedRule);
        $this->assertTrue($publishedRule->isPublished());
        $this->assertTrue($publishedRule->isEnabled());

        try {
            $this->layoutResolverService->loadRuleDraft($rule->getId());
            self::fail('Draft rule still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRuleWithNoLayout()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);
        $this->layoutResolverService->updateRule(
            $rule,
            new RuleUpdateStruct(array('layoutId' => 0))
        );

        $publishedRule = $this->layoutResolverService->publishRule($rule);

        $this->assertInstanceOf(Rule::class, $publishedRule);
        $this->assertTrue($publishedRule->isPublished());
        $this->assertFalse($publishedRule->isEnabled());

        try {
            $this->layoutResolverService->loadRuleDraft($rule->getId());
            self::fail('Draft rule still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRuleWithNoTargets()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $this->layoutResolverService->deleteTarget(
            $this->layoutResolverService->loadTargetDraft(9)
        );

        $this->layoutResolverService->deleteTarget(
            $this->layoutResolverService->loadTargetDraft(10)
        );

        $publishedRule = $this->layoutResolverService->publishRule($rule);

        $this->assertInstanceOf(Rule::class, $publishedRule);
        $this->assertTrue($publishedRule->isPublished());
        $this->assertFalse($publishedRule->isEnabled());

        try {
            $this->layoutResolverService->loadRuleDraft($rule->getId());
            self::fail('Draft rule still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testPublishRuleThrowsBadStateExceptionWithNonDraftRule()
    {
        $rule = $this->layoutResolverService->loadRule(5);
        $this->layoutResolverService->publishRule($rule);
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
        $rule = $this->layoutResolverService->loadRule(4);

        $enabledRule = $this->layoutResolverService->enableRule($rule);

        $this->assertInstanceOf(Rule::class, $enabledRule);
        $this->assertTrue($enabledRule->isEnabled());
        $this->assertTrue($enabledRule->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testEnableRuleThrowsBadStateExceptionWithNonPublishedRule()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        $this->layoutResolverService->enableRule($rule);
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

        $disabledRule = $this->layoutResolverService->disableRule($rule);

        $this->assertInstanceOf(Rule::class, $disabledRule);
        $this->assertFalse($disabledRule->isEnabled());
        $this->assertTrue($disabledRule->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDisableRuleThrowsBadStateExceptionWithNonPublishedRule()
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        $this->layoutResolverService->disableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDisableRuleThrowsBadStateExceptionIfRuleIsAlreadyDisabled()
    {
        $rule = $this->layoutResolverService->loadRule(4);

        $this->layoutResolverService->disableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     */
    public function testAddTarget()
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route_prefix'
        );

        $targetCreateStruct->value = 'some_route_';

        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $createdTarget = $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );

        $this->assertFalse($createdTarget->isPublished());
        $this->assertInstanceOf(Target::class, $createdTarget);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddTargetThrowsBadStateExceptionOnNonDraftRule()
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route_prefix'
        );

        $targetCreateStruct->value = 'some_route_';

        $rule = $this->layoutResolverService->loadRule(5);

        $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddTargetOfDifferentKindThrowsBadStateException()
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route'
        );

        $targetCreateStruct->value = 'some_route';

        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateTarget
     */
    public function testUpdateTarget()
    {
        $target = $this->layoutResolverService->loadTargetDraft(9);

        $targetUpdateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $targetUpdateStruct->value = 'new_value';

        $updatedTarget = $this->layoutResolverService->updateTarget($target, $targetUpdateStruct);

        $this->assertFalse($updatedTarget->isPublished());
        $this->assertInstanceOf(Target::class, $updatedTarget);

        $this->assertEquals('new_value', $updatedTarget->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateTarget
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateTargetThrowsBadStateExceptionOnNonDraftTarget()
    {
        $target = $this->layoutResolverService->loadTarget(9);

        $targetUpdateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $targetUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateTarget($target, $targetUpdateStruct);
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
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteTarget
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDeleteTargetThrowsBadStateExceptionOnNonDraftTarget()
    {
        $target = $this->layoutResolverService->loadTarget(9);

        $this->layoutResolverService->deleteTarget($target);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addCondition
     */
    public function testAddCondition()
    {
        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'ez_site_access'
        );

        $conditionCreateStruct->value = 'cro';

        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $createdCondition = $this->layoutResolverService->addCondition(
            $rule,
            $conditionCreateStruct
        );

        $this->assertFalse($createdCondition->isPublished());
        $this->assertInstanceOf(Condition::class, $createdCondition);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addCondition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddConditionThrowsBadStateExceptionOnNonDraftRule()
    {
        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'ez_site_access'
        );

        $conditionCreateStruct->value = 'cro';

        $rule = $this->layoutResolverService->loadRule(5);

        $this->layoutResolverService->addCondition(
            $rule,
            $conditionCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateCondition
     */
    public function testUpdateCondition()
    {
        $condition = $this->layoutResolverService->loadConditionDraft(4);

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $updatedCondition = $this->layoutResolverService->updateCondition($condition, $conditionUpdateStruct);

        $this->assertFalse($updatedCondition->isPublished());
        $this->assertInstanceOf(Condition::class, $updatedCondition);

        $this->assertEquals('new_value', $updatedCondition->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateCondition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateConditionThrowsBadStateExceptionOnNonDraftCondition()
    {
        $condition = $this->layoutResolverService->loadCondition(4);

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateCondition($condition, $conditionUpdateStruct);
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
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteCondition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDeleteConditionThrowsBadStateExceptionOnNonDraftCondition()
    {
        $condition = $this->layoutResolverService->loadCondition(4);
        $this->layoutResolverService->deleteCondition($condition);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newRuleCreateStruct
     */
    public function testNewRuleCreateStruct()
    {
        $this->assertEquals(
            new RuleCreateStruct(),
            $this->layoutResolverService->newRuleCreateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newRuleUpdateStruct
     */
    public function testNewRuleUpdateStruct()
    {
        $this->assertEquals(
            new RuleUpdateStruct(),
            $this->layoutResolverService->newRuleUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newRuleMetadataUpdateStruct
     */
    public function testNewRuleMetadataUpdateStruct()
    {
        $this->assertEquals(
            new RuleMetadataUpdateStruct(),
            $this->layoutResolverService->newRuleMetadataUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newTargetCreateStruct
     */
    public function testNewTargetCreateStruct()
    {
        $createStruct = $this->layoutResolverService->newTargetCreateStruct('target');
        $createStruct->value = '42';

        $this->assertEquals(
            new TargetCreateStruct(
                array(
                    'type' => 'target',
                    'value' => '42',
                )
            ),
            $createStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newTargetUpdateStruct
     */
    public function testNewTargetUpdateStruct()
    {
        $updateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $updateStruct->value = '42';

        $this->assertEquals(
            new TargetUpdateStruct(
                array(
                    'value' => '42',
                )
            ),
            $updateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newConditionCreateStruct
     */
    public function testNewConditionCreateStruct()
    {
        $createStruct = $this->layoutResolverService->newConditionCreateStruct('condition');
        $createStruct->value = 42;

        $this->assertEquals(
            new ConditionCreateStruct(
                array(
                    'type' => 'condition',
                    'value' => '42',
                )
            ),
            $createStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newConditionUpdateStruct
     */
    public function testNewConditionUpdateStruct()
    {
        $updateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $updateStruct->value = '42';

        $this->assertEquals(
            new ConditionUpdateStruct(
                array(
                    'value' => '42',
                )
            ),
            $updateStruct
        );
    }
}
